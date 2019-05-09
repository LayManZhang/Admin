<?php

namespace app\index\controller\cms;

use app\common\controller\Frontend;
use addons\cms\model\Diyform as DiyformModel;

/**
 * 会员中心
 */
class Diyform extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 表单
     */
    public function index()
    {
        $diyname = $this->request->param('diyname');
        $id = $this->request->param('id','');
        if ($diyname && !is_numeric($diyname)) {
            $diyform = DiyformModel::getByDiyname($diyname);
        } else {
            $id = $diyname ? $diyname : $this->request->get('id', '');
            $diyform = DiyformModel::get($id);
        }
        if (!$diyform || $diyform['status'] == 'hidden') {
            $this->error(__('表单未找到'));
        }
        $fields = DiyformModel::getDiyformFields($diyform['id']);
        $type = !empty($id) ? 'edit' : 'add';
        $this->view->assign('diyform', $diyform);
        $this->view->assign('fields', $fields);
        $this->view->assign('id', $id);
        $this->view->assign('type', $type);

        return $this->view->fetch();
    }

    //项目评估列表
    public function assessment_list(){
        $fieldsList = \app\admin\model\cms\Fields::where('diyform_id',2)->where('type', '<>', 'text')->select();
        $fieldsArr = [];
        $SalesProceedsArr = [];
        foreach ($fieldsList as $key=>$value){
            if($value['name']=='fields'){
                $fieldsArr = $value['content_list'];
            }
            if($value['name']=='SalesProceeds'){
                $SalesProceedsArr = $value['content_list'];
            }
        }

        $xmpgList = db('cms_assessment')
            ->where('user_id', $this->auth->id)
            ->order('id', 'asc')
            ->paginate(10);
        $items = $xmpgList->items();
        foreach ($items as $key=>$val){
            if(!empty($val['fields'])&&$val['fields']!==''){
                $fields = explode(',',$val['fields']);
                $fields_text = [];
                foreach ($fields as $k=>$v){
                    $fields_text[] = $fieldsArr[$v];
                }
                $items[$key]['fields_text'] = implode(',',$fields_text);
                $items[$key]['SalesProceeds_text'] = $val['SalesProceeds'];
            }else{
                $items[$key]['fields_text'] = '';
                $items[$key]['SalesProceeds_text'] = '';
            }
        }
        $this->view->assign('xmpgList', $xmpgList);
        $this->view->assign('items', $items);
        $this->view->assign('title', '项目评估');

        return $this->fetch();

    }

    /**
     * 提交
     */
    public function post()
    {
        $this->request->filter('strip_tags');
        if ($this->request->isPost()) {
            $diyname = $this->request->post('__diyname__');
            $token = $this->request->post('__token__');
            $type = $this->request->post('type');
            $id = $this->request->post('id');
            if (session('__token__') != $token) {
                $this->error("Token不正确！", null, ['token' => $this->request->token()]);
            }
            $diyform = DiyformModel::getByDiyname($diyname);
            if (!$diyform || $diyform['status'] != 'normal') {
                $this->error(__('表单未找到'));
            }
            if ($diyform['needlogin'] && !$this->auth->id) {
                $this->error(__('请登录后再操作'));
            }
            $row = $this->request->post('row/a');

            $fields = DiyformModel::getDiyformFields($diyform['id']);
            foreach ($fields as $index => $field) {
                if ($field['isrequire'] && (!isset($row[$field['name']]) || $row[$field['name']] == '')) {
                    $this->error("{$field['title']}不能为空！", null, ['token' => $this->request->token()]);
                }
            }
            $row['user_id'] = $this->auth->id;
            $row['createtime'] = time();
            $row['updatetime'] = time();
            foreach ($row as $index => &$value) {
                if (is_array($value) && isset($value['field'])) {
                    $value = json_encode(\app\common\model\Config::getArrayData($value), JSON_UNESCAPED_UNICODE);
                } else {
                    $value = is_array($value) ? implode(',', $value) : $value;
                }
            }
            $where = 'pro_status = 1 ';//项目状态执行中

            if(isset($row['fields'])){
                $where .= ' AND (';
                $fields = explode(',',$row['fields']);
                foreach ($fields as $item){
                    $where .= " FIND_IN_SET($item,b.Fields) OR ";
                }
                $where = substr($where,0,-4);
                $where .= ' )';
            }
            if(isset($row['District'])) {//所在区域筛选项目级别是国家，省，市，以及所选区
                    $where .= 'AND (FIND_IN_SET('.$row['District'].',b.xmjb) or FIND_IN_SET(1,b.xmjb) or FIND_IN_SET(2,b.xmjb) or FIND_IN_SET(3,b.xmjb))';
            }
            if(isset($row['SalesProceeds'])) {//营业额  大于等于低区间，如果高区间大于0则低于高区间
                    $where .= 'AND b.SalesProceeds <= '.$row['SalesProceeds'] .'  AND IF(Max_SalesProceeds>0,'.$row['SalesProceeds'].'< Max_SalesProceeds,1=1)';
            }
            if(isset($row['Ratal'])) {//纳税额
                    $where .= ' AND b.Ratal <= ' . $row['Ratal'];
            }
            if(isset($row['Researchput'])) {//研发投入
                    $where .= ' AND b.Researchput <= ' . $row['Researchput'];
            }
            if(isset($row['Brandput'])) {//品牌投入
                    $where .= ' AND b.Brandput <= ' . $row['Brandput'];
            }
            if(isset($row['Equipmentput'])) {//设备投入
                    $where .= ' AND b.Equipmentput <= ' . $row['Equipmentput'];
            }
            if(isset($row['Informationization'])) {//信息化投入
                    $where .= ' AND b.Informationization <= ' . $row['Informationization'];
            }
            //根据条件查找符合的项目
            $data = db('cms_archives')->alias('a')->join('cms_addonxmzn b','a.id = b.id')->field(['b.*','a.title','a.id'])->where($where)->select();
            if(empty($data)){
                return [];
            }
            $ids = [];
            foreach ($data as $val){
                $ids[] = $val['id'];
            }
            $ids = implode(',',$ids);
            $row['Project'] = $ids;
            try {

                if($type=='add'){
                    \think\Db::name($diyform['table'])->insert($row);
                }else if($type=='edit'){
                    \think\Db::name($diyform['table'])->where('id',$id)->update($row);
                }

            } catch (Exception $e) {
                $this->error("发生错误:" . $e->getMessage());
            }
            //$this->success($diyform['successtips'] ? $diyform['successtips'] : '提交成功！', url('index/cms.diyform/assessment_list'));
        }
        return json($data);

    }


    public function get_xmzn(){
        $fieldsList = \app\admin\model\cms\Fields::where('model_id',3)->where('type', '<>', 'text')->select();
        $fieldsArr = [];
        foreach ($fieldsList as $key=>$value){
            if($value['name']=='Objects'){
                $fieldsArr['Objects'] = $value['content_list'];
            }
            if($value['name']=='Mode'){
                $fieldsArr['Mode'] = $value['content_list'];
            }
            if($value['name']=='Direction'){
                $fieldsArr['Direction'] = $value['content_list'];
            }
            if($value['name']=='Fields'){
                $fieldsArr['Fields'] = $value['content_list'];
            }

        }
//        print_r($fieldsArr);exit;
        $id = $this->request->get('id');
        $row = db('cms_assessment')->where('id',$id)->find();
        $Project = $row['Project'];
            //根据条件查找符合的项目
        $where['a.id'] = array('in',$Project);
        $data = db('cms_archives')->alias('a')->join('cms_addonxmzn b','a.id = b.id')->field(['b.*','a.title'])->where($where)->select();
        foreach ($data as $key=>$val) {
            $Objects = explode(',',$val['Objects']);
            $Mode = explode(',',$val['Mode']);
            $Direction = explode(',',$val['Direction']);
            $Fields = explode(',',$val['Fields']);
            foreach ($Objects as $item){
                $ObjectsArr[] = $fieldsArr['Objects'][$item];
            }
            foreach ($Mode as $item){
                $ModeArr[] = $fieldsArr['Mode'][$item];
            }
            foreach ($Direction as $item){
                $DirectionArr[] = $fieldsArr['Direction'][$item];
            }
            foreach ($Fields as $item){
                $FieldsArr[] = $fieldsArr['Fields'][$item];
            }
            $data[$key]['Objects_text'] = implode(',',$ObjectsArr);
            $data[$key]['Mode_text'] = implode(',',$ModeArr);
            $data[$key]['Direction_text'] = implode(',',$DirectionArr);
            $data[$key]['Fields_text'] = implode(',',$FieldsArr);
        }
        return json($data);
    }

    //项目评审详情
    public function get_xmps(){
        $id = $this->request->get('id');
        $data = db('cms_assessment')->where('id',$id)->find();
        return json($data);
    }

    //获取PDF数据
    public function getdata(){
        $id = $this->request->get('id');
        $ids = $this->request->get('ids');

        if(isset($id)&&!empty($id)){
            $row = db('cms_assessment')->where('id',$id)->find();
            $Project = $row['Project'];
            //根据条件查找符合的项目
            $where['a.id'] = array('in',$Project);
        }else if(isset($ids)&&!empty($ids)){
            $where['a.id'] = array('in',$ids);
        }
        $data_arr = [];
        $data = db('cms_archives')->alias('a')->join('cms_addonxmzn b','a.id = b.id')->field(['b.*','a.title'])->where($where)->group('xmjb')->select();
        foreach ($data as $key=>$val){
            $xmjb = explode(',',$val['xmjb']);
            $object = array_filter($xmjb);
            $length = count($object);
            $xmjb = $object[$length-2];
            if(isset($xmjb)&&!empty($xmjb)){
                $where['xmjb'] = $val['xmjb'];
                $xmjb_data = db('cms_xmjb')->where('id',$xmjb)->find();
                $data_arr[$key]['id'] = $xmjb;
                $data_arr[$key]['name'] = $xmjb_data['grade_fname'];
                $data_arr[$key]['data'] = db('cms_archives')->alias('a')->join('cms_addonxmzn b','a.id = b.id')->field(['b.*','a.title'])->where($where)->select();
            }
//            else if(isset($xmjb[1])&&!empty($xmjb[1])){
//                $where['xmjb'] = $val['xmjb'];
//                $xmjb_data = db('cms_xmjb')->where('id',$xmjb[1])->find();
//                $data_arr[$key]['id'] = $xmjb[2];
//                $data_arr[$key]['name'] = $xmjb_data['grade_fname'];
//                $data_arr[$key]['data'] = db('cms_archives')->alias('a')->join('cms_addonxmzn b','a.id = b.id')->field(['b.*','a.title'])->where($where)->select();
//            }
        }
        $this->pdf($data_arr);
    }

    //导出评估结果PDF
    public function pdf($data){

        $pdf = new \MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('中科创');
        $pdf->SetTitle('评估结果');
        $pdf->SetSubject('项目评估');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('droidsansfallback', '', 12);

        // add a page
        $pdf->AddPage();
        $i = 1;
        foreach ($data as $key=>$val){

            $pdf->Bookmark($i.' '.$val['name'], 0, 0, '', 'B', array(0,64,128));
            $pdf->Cell(0, 10, $i.' '.$val['name'], 0, 1, 'L');
            $pdf->Ln(2);
            foreach ($val['data'] as $k=>$item){
                $k= $k+1;
                $pdf->Bookmark($i.'-'.$k.' '.$item['title'], 1, 0, '', 'B', array(128,0,0));
                $pdf->Cell(0, 10, $i.'-'.$k.' '.$item['title'], 0, 1, 'L');
                $pdf->Ln(1);
                $pdf->SetFont('droidsansfallback', '', 12);
                $html = '<table border="1">
                                <tbody>
                                <tr>
                                  <td style="text-align: center;width: 150px;">项目名称:</td>
                                  <td style="width: 500px">'.$item['title'].'</td>
                                </tr>
                                <tr>
                                  <td style="text-align: center;width: 150px;">营业额要求:</td>
                                  <td>'.$item['SalesProceeds'].'</td>
                                </tr>
                                <tr>
                                  <td style=" text-align: center;width: 150px;">纳税要求:</td>
                                  <td>'.$item['Ratal'].'</td>
                                </tr>
                                <tr>
                                  <td style="text-align: center;width: 150px;">资助金额:</td>
                                  <td>'.$item['Amount'].'</td>
                                </tr>
                                <tr>
                                  <td style="text-align: center;width: 150px;">申请时间:</td>
                                  <td>'.$item['Starttime'].'-'.$item['Endtime'].'</td>
                                </tr>
                                <tr>
                                  <td style="text-align: center;width: 150px;">项目简介:</td>
                                  <td>'.$item['Introduction'].'</td>
                                </tr>
                                </tbody>          
                        </table>';
                $pdf->writeHTML($html, true, false, true, false, '');

            }
            $i++;
        }

        // add a new page for TOC
        $pdf->addTOCPage();

        // write the TOC title
        $pdf->SetFont('droidsansfallback', 'B', 16);
        $pdf->MultiCell(0, 0, '目录', 0, 'C', 0, 1, '', '', true, 0);
        $pdf->Ln();

        $pdf->SetFont('droidsansfallback', '', 12);

        // add a simple Table Of Content at first page
        $pdf->addTOC(1, 'courier', '.', 'INDEX', 'B', array(128,0,0));

        // end of TOC page
        $pdf->endTOCPage();


        // set font
        $pdf->SetFont('droidsansfallback', 'BI', 12);

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('评估结果.pdf', 'D');
    }
}
