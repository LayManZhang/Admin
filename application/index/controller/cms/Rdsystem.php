<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 2019/3/29
 * Time: 16:26
 */

namespace app\index\controller\cms;

use app\common\controller\Frontend;
use PhpOffice\PhpWord\TemplateProcessor;
use think\Db;
use addons\cms\model\Diyform as DiyformModel;
use think\Exception;

class Rdsystem extends Frontend
{
    protected $layout = 'default';
    protected $noNeedLogin = ['dcpdf'];
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

        return $this->view->fetch('');
    }

    /**
     * 添加
     */
    public function add()
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
            try {
                if($type=='add'){
                    \think\Db::name($diyform['table'])->insert($row);
                }else if($type=='edit'){
                    \think\Db::name($diyform['table'])->where('id',$id)->update($row);
                    if($diyname=='Research'){
                        Db::name('rddetail')->where('rid',$id)->update(['date'=>$row['Date'],'type'=>$row['Category']]);
                    }else if($diyname=='Research_system'){
                        Db::name('cms_userwords')->where('user_id',$this->auth->id)->update(['is_new'=>0,'update_time'=>time()]);
                    }
                }
                return json(['code'=>1,'msg'=>$type=='add' ? '添加成功' : '编辑成功']);
            } catch (Exception $e) {
                $this->error("发生错误:" . $e->getMessage());
            }
            return json(['code'=>1,'msg'=>$type=='add' ? '添加失败' : '编辑编辑失败']);
        }
    }

    public function detail(){
        $id = $this->request->get('id');
        $diyname = $this->request->param('diyname');
        $diyform = DiyformModel::getByDiyname($diyname);
        $data = Db::name($diyform['table'])->where(['id'=>$id,'user_id'=>$this->auth->id])->find();
        return json($data);
    }

    public function delete(){
        $id = $this->request->post('id');
        $diyname = $this->request->param('diyname');
        $diyform = DiyformModel::getByDiyname($diyname);

        Db::startTrans();
        try {
            $data = Db::name($diyform['table'])->where(['id'=>['in',$id],'user_id'=>$this->auth->id])->delete();
        if($diyname=='Project') {
            Db::name('rddetail')->where(['pid' => ['in', $id], 'user_id' => $this->auth->id])->delete();
        }else{
            Db::name('rddetail')->where(['rid'=>['in',$id],'user_id'=>$this->auth->id])->delete();
        }
            Db::commit();
            return json(['code'=>1,'msg'=>'删除成功']);
        } catch (\Exception $e) {
            Db::rollback();
            $this->error("发生错误:" . $e->getMessage());
        }
    }

    public function research(){
        $limit = $this->request->param('limit',10);
        $data = Db::name('research_ledger')
            ->where('user_id',$this->auth->id)
            ->order('id', 'asc')
            ->paginate($limit);
        $items = $data->items();

        //明细账费用类别
        $typeArr = [11=>['key'=>'Laborcosts_salary','name'=>'工资薪金'],12=>['key'=>'Laborcosts_si','name'=>'五险一金'],13=>['key'=>'Laborcosts_services','name'=>'外聘研发人员的劳务费'],
            21=>['key'=>'Directinput_materials','name'=>'材料费'],22=>['key'=>'Directinput_fuelcosts','name'=>'燃料费'],23=>['key'=>'Directinput_powercosts','name'=>'动力费用'],
            24=>['key'=>'Directinput_moldcosts','name'=>'试制模具、工艺装备费'],25=>['key'=>'Directinput_samplecosts','name'=>'样品、样机费等'],
            26=>['key'=>'Directinput_inspectioncosts','name'=>'试制产品检验费'],27=>['key'=>'Directinput_maintenancecosts','name'=>'仪器设备运维、检验费等'],
            28=>['key'=>'Directinput_rentalcosts','name'=>'仪器设备租赁费'],31=>['key'=>'Depreciation_instrument','name'=>'仪器折旧'],
            32=>['key'=>'Depreciation_equipment','name'=>'设备折旧'],33=>['key'=>'IA_amortization_software','name'=>'在用建筑物折旧'],34=>['key'=>'IA_amortization_patent','name'=>'长期待摊费用'],
            41=>['key'=>'IA_amortization_nonpatented','name'=>'软件摊销'],42=>['key'=>'Product_design','name'=>'专利权摊销'],
            43>['key'=>'Other1','name'=>'非专利技术摊销'],51=>['key'=>'Other2','name'=>'新产品设计费'],
            52=>['key'=>'Other3','name'=>'新工艺规程制定费'],53=>['key'=>'Other4','name'=>'新药研制的临床试验费'],
            54=>['key'=>'Other5','name'=>'勘探开发技术的现场试验费'],61=>['key'=>'Other5','name'=>'装备调试费用'],62=>['key'=>'Other5','name'=>'田间试验费'],
            71=>['key'=>'Other5','name'=>'技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费'],72=>['key'=>'Other5','name'=>'研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用'],73=>['key'=>'Other5','name'=>'知识产权的申请费、注册费、代理费'],
            74=>['key'=>'Other5','name'=>'差旅费、会议费'],75=>['key'=>'Other5','name'=>'职工福利费、补充养老保险费、补充医疗保险费'],76=>['key'=>'Other5','name'=>'通讯费'],
            81=>['key'=>'Other5','name'=>'委托境内研发'],82=>['key'=>'Other5','name'=>'委托境外研发']];
        foreach ($items as $key=>$val){
            $items[$key]['Category'] = $typeArr[$val['Category']]['name'];
        }
        $this->view->assign('items',$items);
        $this->view->assign('data',$data);
        $this->view->assign('title', '明细账');
        return $this->view->fetch();
    }


    public function project(){
        $limit = $this->request->param('limit',10);
        $year = $this->request->param('year');
        if(!empty($year)&&$year!==null&&$year!==''){
            $where = "user_id = ".$this->auth->id ." AND ( DATE_FORMAT(QH34,'%Y') = ".$year .' or '. "DATE_FORMAT(QH35,'%Y') = ".$year.")";
        }else{
            $where = 'user_id = '.$this->auth->id;
        }
        $data = Db::name('project')
//            ->where('user_id',$this->auth->id)
            ->whereRaw($where)
            ->order('id', 'asc')
            ->paginate($limit);
        $items = $data->items();
        $this->assign('year',$year);
        $this->assign('items',$items);
        $this->assign('data',$data);
        $this->view->assign('title', '研发项目');
        return $this->fetch();
    }

    public function costdetail(){
        $year = $this->request->param('year',2019);
        $type = $this->request->param('type',1);

        //查询符合年份的明细账并且存在符合项目的
        //项目表为主表 再查明细账 明细账日期在项目起止日期内的
        $Pro_data = Db::name('project')
            ->where('user_id',$this->auth->id)
            ->whereRaw("DATE_FORMAT(QH34,'%Y') = ".$year .' or '. "DATE_FORMAT(QH35,'%Y') = ".$year)
            ->field(['id','project_number','project_name','QH34','QH35'])
            ->select();
        if($Pro_data){
            foreach ($Pro_data as $key=>$val){
                $data = Db::name('research_ledger')
                    ->where('user_id',$this->auth->id)
                    ->whereRaw("DATE_FORMAT(Date,'%Y') = ".$year)
                    ->where('Category',$type)
                    ->order('id', 'asc')
//                    ->paginate(10);
                    ->select();
                foreach ($data as $k=>$v){
                    $detail = Db::name('rddetail')
                        ->where(['rid'=>$v['id'],'type'=>$type])
                        ->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)
                        ->select();
                    foreach ($detail as $item){
                        $data[$k][$item['pid']]['amount'] = $item['amount'];
                        $data[$k][$item['pid']]['rate'] = $item['rate'];
                    }
                }
                $dataAll = $data;
            }
        }else{
            $Pro_data = [];
            $dataAll = [];
            $data = [];
        }

        if ($this->request->isAjax()) {
            $res['project'] = $Pro_data;
            $res['data'] = $dataAll;
            $res['code'] = 1;
            $res['msg'] = '';
            return json($res);
        }

        $this->assign('pro',$Pro_data);
        $this->assign('items',$dataAll);
//        $this->assign('items',$items);
        $this->view->assign('title', '研发费用明细表');
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 编辑研发项目详情
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editcostdetil(){
        $rid = $this->request->post('rid');
        $pid = $this->request->post('pid');
        $amount = $this->request->post('amount');
//        $rate = $this->request->post('rate');
        $type = $this->request->post('type');

        $research = Db::name('research_ledger')->where(['user_id'=>$this->auth->id,'id'=>$rid])->field(['Debit_amount','Credit_amount','Date'])->find();
        if($research){
            if((float)$research['Debit_amount']!==0&&(float)$research['Credit_amount']==0){
                $lendstate = 1;
            }else if((float)$research['Debit_amount']==0&&(float)$research['Credit_amount']!==0){
                $lendstate = 2;
            }else{
                $lendstate = 0;
            }
        }

        $row = [
            'user_id'=>$this->auth->id,
            'rid'=>$rid,
            'pid'=>$pid,
            'type'=>$type,
//            'rate'=>$rate,
            'amount'=>$amount,
            'lend_state'=>$lendstate,
            'date'=>$research['Date']
        ];

        $data = Db::name('rddetail')->where(['pid'=>$pid,'rid'=>$rid,'type'=>$type,'user_id'=>$this->auth->id])->find();
        if($data){
            $update = Db::name('rddetail')->where(['pid'=>$pid,'rid'=>$rid,'type'=>$type,'user_id'=>$this->auth->id])->update(['amount'=>$amount,'lend_state'=>$lendstate]);
        }else{
            $update = Db::name('rddetail')->insert($row);
        }
        if($update){
            $res = ['code'=>1,'msg'=>'编辑成功'];
        }else{
            $res = ['code'=>1,'msg'=>'编辑成功'];
        }
        return json($res);
    }

    /**
     * 结构明细表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function structlist(){
        $year = $this->request->param('year',2019);


        $projects = Db::name('project')
            ->alias('a')
            ->join('rddetail b','b.pid = a.id','INNER')
            ->where('a.user_id',$this->auth->id)
            ->whereRaw("DATE_FORMAT(b.date,'%Y') = ".$year)
            ->field(['a.id','project_name','project_number'])
            ->group('b.pid')
            ->paginate(10);
        $project = $projects->items();
        foreach ($project as $key=>$val){
            $totalA = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->field(Db::raw("SUM(amount) as total"))->find();
            $ryrgfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','11,12,13')->field(Db::raw("SUM(amount) as total"))->group('pid')->find();
            $zjtrfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','21,22,23,24,25,26,27,28')->field(Db::raw("SUM(amount) as total"))->find();
            $zjfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','31,32,33,34')->field(Db::raw("SUM(amount) as total"))->find();
            $wxzctxfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','41,42,43')->field(Db::raw("SUM(amount) as total"))->find();
            $xcpsjfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','51,52')->field(Db::raw("SUM(amount) as total"))->find();
            $zbfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','53,54,61,62')->field(Db::raw("SUM(amount) as total"))->find();
            $other = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','71,72,73,74,75,76')->field(Db::raw("SUM(amount) as total"))->find();
            $wtyffy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','81,82')->field(Db::raw("SUM(amount) as total"))->find();
            $jnyffy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','81')->field(Db::raw("SUM(amount) as total"))->find();

            $project[$key]['totalA'] = $totalA['total']==null ? '0' : $totalA['total'];
            $project[$key]['ryrgfy'] = $ryrgfy['total']==null ? '0' : $ryrgfy['total'];
            $project[$key]['zjtrfy'] = $zjtrfy['total']==null ? '0' : $zjtrfy['total'];
            $project[$key]['zjfy'] = $zjfy['total']==null ? '0' : $zjfy['total'];
            $project[$key]['wxzctxfy'] = $wxzctxfy['total']==null ? '0' : $wxzctxfy['total'];
            $project[$key]['xcpsjfy'] = $xcpsjfy['total']==null ? '0' : $xcpsjfy['total'];
            $project[$key]['zbfy'] = $zbfy['total']==null ? '0' : $zbfy['total'];
            $project[$key]['other'] = $other['total']==null ? '0' : $other['total'];
            $project[$key]['wtyffy'] = $wtyffy['total']==null ? '0' : $wtyffy['total'];
            $project[$key]['jnyffy'] = $jnyffy['total']==null ? '0' : $jnyffy['total'];

        }
        if($this->request->isAjax()){
            return json($project);
        }
        $this->view->assign('title', '结构明细表');
        return $this->fetch();
    }

    public function reporting(){
        $limit = $this->request->param('limit',10);
        $year = $this->request->param('year');
        if(!empty($year)&&$year!==null&&$year!==''){
            $where = "user_id = ".$this->auth->id ." AND ( DATE_FORMAT(QH34,'%Y') = ".$year .' or '. "DATE_FORMAT(QH35,'%Y') = ".$year.")";
        }else{
            $where = 'user_id = '.$this->auth->id .' and QH34 = ""';
        }
        $data = Db::name('project')
//            ->where('user_id',$this->auth->id)
            ->whereRaw($where)
            ->order('id', 'asc')
            ->paginate($limit);
        $items = $data->items();
        $this->assign('year',$year);
        $this->assign('items',$items);
        $this->assign('data',$data);
        $this->view->assign('title', '研发项目');
        return $this->fetch();
    }

    public function importtips()
    {
        $this->assign('title','Excel模板');
        return $this->fetch();
    }

    public function download_template(){
        $file = $this->request->param('file');
        $file_arr = [
            'mxz'=>['url'=>'/excel/mxz.xls','name'=>'明细账导入模板'],
            'fymx'=>['url'=>'/excel/yffymxz.xls','name'=>'明细账导入费用明细'],
            'xm'=>['url'=>'/excel/yfxm.xls','name'=>'研发项目导入模板']
        ];
        $file_url = ROOT_PATH.'public'.$file_arr[$file]['url'];
        $file_name = $file_arr[$file]['name'];
       $this->download($file_url,$file_name);
    }

    /**
     * 导入
     */
    public function import()
    {
        $file = $this->request->file('file');
        $diyname = $this->request->post('diyname');
        ini_set("error_reporting","E_ALL & ~E_NOTICE");
        if (empty($file)) {
            $this->error(__('未上传文件'));
        }
        if($diyname=='Research'){
            $this->model = new \app\admin\model\cms\Researchlledger();
        }else if($diyname=='Project'){
            $this->model = new \app\admin\model\cms\Project();
        }else{
            $this->error(__('表单未找到'));
        }
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = $file->getInfo('tmp_name');
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath)) {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $fields[] = $val;
            }
        }
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {//$currentRow 取值行
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                if(substr($val,0,1) == '='){
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getFormattedValue();
                }
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);

            foreach ($temp as $k => $v) {
                if (isset($fieldArr[$k]) && $k !== '') {
                    if($fieldArr[$k]=='Date' || $fieldArr[$k]=='QH34' || $fieldArr[$k]=='QH35') {
                        $row[$fieldArr[$k]] = gmdate('Y/m/d', \PHPExcel_Shared_Date::ExcelToPHP($v));
                    }else{
                        $row[$fieldArr[$k]] = $v;
                    }

                }
            }
            if($diyname=='Research'){
                if ($row&&!empty($row['Date'])&&!empty($row['Document_number'])) {
                    $row['user_id'] = $this->auth->id;
                    $insert[] = $row;
                }
            }else if($diyname=='Project'){
                if ($row&&!empty($row['project_number'])) {
                    $row['user_id'] = $this->auth->id;
                    $insert[] = $row;
                }
            }

        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }
        try {
            $this->model->saveAll($insert);
            $res = json(['code'=>1,'msg'=>'导入成功']);
        } catch (\think\exception\PDOException $exception) {
            $this->error($exception->getMessage());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $res;
    }

    /**
     * 导入费用明细
     * @return \think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function importAll()
    {
        $file = $this->request->file('file');
        ini_set("error_reporting","E_ALL & ~E_NOTICE");

        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = $file->getInfo('tmp_name');
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath)) {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        //获取工作表的数目
        $sheetCount = $PHPExcel->getSheetCount();
        $insertR = [];
        $insertP = [];
            for ( $i = 0; $i < 2; $i++ ) {
                switch ($i){
                    case 0:
                        $this->model = new \app\admin\model\cms\Project();
                        break;
                    case 1:
                        $this->model = new \app\admin\model\cms\Researchlledger();
                        break;
                    default:
                        $this->error(__('表单未找到'));
                        break;
                }
                //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
                $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

                $table = $this->model->getQuery()->getTable();
                $database = \think\Config::get('database.database');
                $fieldArr = [];
                $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
                foreach ($list as $k => $v) {
                    if ($importHeadType == 'comment') {
                        $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
                    } else {
                        $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
                    }
                }


                $currentSheet = $PHPExcel->getSheet($i);  //读取文件中的第一个工作表
                $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
                $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
                $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
                for ($i==1 ? $currentRow = 4 : $currentRow = 2;$i==1 ? $currentRow <= 4 :  $currentRow <= 2 ; $currentRow++) {
                    for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                        $fields[] = $val;
                    }
                }

                $insert = [];

                for ($i==1?$currentRow = 6:$currentRow = 4; $currentRow <= $allRow; $currentRow++) {//$currentRow 取值行
                    $values = [];
                    for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                        $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();

                        if(is_object($val)){
                            $val = $val->__toString();
                        }
                        if (substr($val, 0, 1) == '=') {
                            $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getFormattedValue();
                        }

                        $values[] = is_null($val) ? '' : $val;

                    }
                    $row = [];
                    if($i==1){
                        for ($j=0; $j<11; $j++){
                            unset($fields[$j]);
                        }
                        $temp = array_combine($fields, $values);
                    }else{
                        $temp = array_combine($fields, $values);
                    }


                    foreach ($temp as $k => $v) {

                        if (isset($fieldArr[$k]) && $k !== '') {
                            if($fieldArr[$k]=='Category' || $fieldArr[$k]=='QH11' || $fieldArr[$k]=='QH31' || $fieldArr[$k]=='QH21' || $fieldArr[$k]=='QH32' || $fieldArr[$k]=='QH33') {
                                $row[$fieldArr[$k]] = explode('.',$v)[0];
                            }else{
                                $row[$fieldArr[$k]] = $v;
                            }
                        }else{
                            $row[$k] = $v;
                        }

                    }
                    if ($row) {
                        if($i==1&&!empty($row['Document_number'])){
                            if(is_float($row['Date'])){
                                $row['Date'] = gmdate('Y/m/d', \PHPExcel_Shared_Date::ExcelToPHP($row['Date']));
                            }
                            $row['user_id'] = $this->auth->id;
                            $insertR[] = $row;
                            //存在结转损益凭证号的添加一条记录凭证日期是当月最后一天，其他相同。
                            if(!empty($row['结转损益凭证号'])){
                                $date = date('Y/m/01',strtotime($row['Date']));
                                $jzdate = date('Y/m/d', strtotime("$date +1 month -1 day"));
                                $jz_data = $row;
                                $jz_data['Date']=$jzdate;
                                $jz_data['Document_number']=$row['结转损益凭证号'];
                                $jz_data['Abstract']='结转本期损益';
                                $jz_data['Debit_amount']='0';
                                $jz_data['Credit_amount']=$row['Debit_amount'];
                                $insertR[] = $jz_data;
                            }
                        }else if($i==0&&!empty($row['project_number'])){
                            if(is_float($row['QH34'])){
                                $row['QH34'] = gmdate('Y/m/d', \PHPExcel_Shared_Date::ExcelToPHP($row['QH34']));
                            }
                            if(is_float($row['QH35'])){
                                $row['QH35'] = gmdate('Y/m/d', \PHPExcel_Shared_Date::ExcelToPHP($row['QH35']));
                            }
                            $row['user_id'] = $this->auth->id;
                            $insertP[] = $row;
                        }
                    }
                }
            }

        //开启事物回滚机制
        Db::startTrans();
        try{
            $projectArr = [];
            foreach ($insertP as $k=>$v){
                $pdata = [
                    'project_number'=>$v['project_number'],
                    'project_name'=>$v['project_name'],
                    'QH34'=>$v['QH34'],
                    'QH35'=>$v['QH35'],
                    'Budget'=>$v['Budget'],
                    'QH11'=>$v['QH11'],
                    'QH31'=>$v['QH31'],
                    'QH21'=>$v['QH21'],
                    'QH32'=>$v['QH32'],
                    'QH33'=>$v['QH33'],
                    'user_id'=>$v['user_id'],
                    'createtime'=>time(),
                    'updatetime'=>time(),
                ];
                $pid = Db::name('project')->insertGetId($pdata);
                $projectArr[$k] = ['id'=>$pid,'name'=>$v['project_name']];
            }
            foreach ($insertR as $key=>$val){
                $rdata = [
                    'user_id'=>$val['user_id'],
                    'Date'=>$val['Date'],
                    'Document_number'=>$val['Document_number'],
                    'Acct_Tit'=>$val['Acct_Tit'],
                    'Abstract'=>$val['Abstract'],
                    'Debit_amount'=>$val['Debit_amount'],
                    'Credit_amount'=>$val['Credit_amount'],
                    'Category'=>$val['Category'],
                    'createtime'=>time(),
                    'updatetime'=>time(),
                    ];
                $rid = Db::name('research_ledger')->insertGetId($rdata);
                if(!empty($val['Debit_amount'])&&(float)$val['Debit_amount']!==0){
                    $lend_state = 1;
                }else{
                    $lend_state = 2;
                }

                $i = 4;
                foreach ($projectArr as $item){
                    $name = '=项目明细!B'.$i;
                    if(isset($val[$name]) && !empty($val[$name])){
                        $amount = $val[$name]=='' ? '0.00' :  $val[$name];
                        $amount = $lend_state==2 ? '-'.$amount : $amount;
                        $data = [
                            'user_id'=>$this->auth->id,
                            'pid'=>$item['id'],
                            'rid'=>$rid,
                            'type'=>$val['Category'],
                            'amount'=>$amount,
                            'lend_state'=>$lend_state,
                            'date'=>$val['Date']
                        ];
                        Db::name('rddetail')->insert($data);
                    }
                    $i++;
                }
            }
            $res = json(['code'=>1,'msg'=>'导入成功']);
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            $res = json(['code'=>0,'msg'=>'导入失败']);
        }
        return $res;
    }


    /**
     * 导出excel
     */
    public function ExportExcel(){

        $id = $this->request->param('id');
        $year = $this->request->param('year');

        $obj  = new \PHPExcel();

        $ids = explode(',',$id);
        foreach ($ids as $key=>$v){
            $project = Db::name('project')->where(['user_id'=>$this->auth->id,'id'=>$v])->field(['id','project_number','project_name'])->find();
//            var_dump($project);exit;
            //横向单元格标识

            $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

            $obj->getDefaultStyle()->getFont()->setName('宋体');

            if($key !== 0) $obj->createSheet();
            $obj->setactivesheetindex($key);

            $obj->getActiveSheet($key)->setTitle($project['project_name']);   //设置sheet名称


            $obj->getActiveSheet($key)->mergeCells('A1'.':'.'AI1');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A1', '自主研发“研发支出”辅助账');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('A2'.':'.'C2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A2', '项目名称:');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('D2'.':'.'G2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('D2', $project['project_name']);  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('H2'.':'.'I2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('H2', '项目编号:');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('J2'.':'.'K2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('J2', $project['project_number']);  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('L2'.':'.'N2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('L2', '资本化、费用化支出选项:');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('O2'.':'.'P2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('O2', '费用化');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('Q2'.':'.'S2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Q2', '项目实施状态选项：');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('T2'.':'.'U2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('T2', '未完成');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('V2'.':'.'W2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('V2', '研发成果：');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('X2'.':'.'Z2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('X2', '');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AA2'.':'.'AB2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AA2', '研发成果证书号：');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AC2'.':'.'AE2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AC2', '');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AF2'.':'.'AI2');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AF2', '金额单位：元（列至角分）：');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('A3'.':'.'B6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A3', '凭证日期');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('C3'.':'.'D6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('C3', '凭证号');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('E3'.':'.'E6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('E3', '摘要');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('F3'.':'.'F6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('F3', '借方金额');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('G3'.':'.'G6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('G3', '贷方金额');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('H3'.':'.'H6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('H3', '借或贷');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('I3'.':'.'I6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('I3', '余额');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('J3'.':'.'AI3');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('J3', '费用明细（借方）');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('J4'.':'.'L4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('J4', '一、人员人工费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('M4'.':'.'T4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('M4', '二、直接投入费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('U4'.':'.'V4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('U4', '三、折旧费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('W4'.':'.'Y4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('W4', '四、无形资产摊销');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('Z4'.':'.'AC4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Z4', '五、新产品设计费等');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AD4'.':'.'AI4');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AD4', '六、其他相关费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('J5'.':'.'K5');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('J5', '直接从事研发活动人员');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('J6'.':'.'J6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('J6', '工资薪金');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('K6'.':'.'K6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('K6', '五险一金');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('L5'.':'.'L6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('L5', '外聘研发人员的劳务费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('M5'.':'.'O5');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('M5', '研发活动直接消耗');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('M6'.':'.'M6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('M6', '材料');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('N6'.':'.'N6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('N6', '燃料');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('O6'.':'.'O6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('O6', '动力费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('P5'.':'.'P6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('P5', '用于中间试验和产品试制的模具、工艺装备开发及制造费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('Q5'.':'.'Q6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Q5', '用于不构成固定资产的样品、样机及一般测试手段购置费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('R5'.':'.'R6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('R5', '用于试制产品的检验费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('S5'.':'.'S6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('S5', '用于研发活动的仪器、设备的运行维护、调整、检验、维修等费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('T5'.':'.'T6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('T5', '通过经营租赁方式租入的用于研发活动的仪器、设备租赁费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('U5'.':'.'U6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('U5', '用于研发活动的仪器的折旧费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('V5'.':'.'V6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('V5', '用于研发活动的设备的折旧费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('W5'.':'.'W6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('W5', '用于研发活动的软件的摊销费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('X5'.':'.'X6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('X5', '用于研发活动的专利权的摊销费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('Y5'.':'.'Y6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Y5', '用于研发活动的非专利技术（包括许可证、专有技术、设计和计算方法等）的摊销费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('Z5'.':'.'Z6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Z5', '新产品设计费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AA5'.':'.'AA6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AA5', '新工艺规程制定费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AB5'.':'.'AB6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AB5', '新药研制的临床试验费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AC5'.':'.'AC6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AC5', '勘探开发技术的现场试验费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AD5'.':'.'AD6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AD5', '技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AE5'.':'.'AE6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AE5', '研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AF5'.':'.'AF6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AF5', '知识产权的申请费、注册费、代理费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AG5'.':'.'AG6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AG5', '差旅费、会议费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AH5'.':'.'AH6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AH5', '职工福利费、补充养老保险费、补充医疗保险费');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('AI5'.':'.'AI6');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AI5', '');  //设置合并后的单元格内容

            $obj->getActiveSheet($key)->mergeCells('A7'.':'.'I7');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A7', '序号');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('J7', '1.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('K7', '1.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('L7', '1.3');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('M7', '2.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('N7', '2.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('O7', '2.3');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('P7', '2.4');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Q7', '2.5');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('R7', '2.6');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('S7', '2.7');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('T7', '2.8');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('U7', '3.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('V7', '3.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('W7', '4.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('X7', '4.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Y7', '4.3');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Z7', '5.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AA7', '5.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AB7', '5.3');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AC7', '5.4');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AD7', '6.1');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AE7', '6.2');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AF7', '6.3');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AG7', '6.4');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AH7', '6.5');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AI7', '6.6');  //设置单元格内容

            $obj->getActiveSheet($key)->mergeCells('A8'.':'.'H8');   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A8', '起初余额');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('I8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('J8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('K8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('L8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('M8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('N8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('O8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('P8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Q8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('R8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('S8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('T8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('U8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('V8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('W8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('X8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Y8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Z8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AA8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AB8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AC8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AD8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AE8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AF8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AG8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AH8', '0.00');  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AI8', '0.00');  //设置单元格内容


            //样式
            $obj->getActiveSheet($key)->getstyle('A1'.':'.'AI1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $obj->getActiveSheet($key)->getstyle('A1')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('A2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('A8')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $obj->getActiveSheet($key)->getstyle('H2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('H2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('L2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('L2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('Q2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('Q2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('V2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('V2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('AA2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('AA2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('AF2')->getFont()->setBold(true);
            $obj->getActiveSheet($key)->getstyle('AF2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('A1')->getFont()->setSize(15);
            $obj->getActiveSheet($key)->getstyle('A3'.':'.'AI7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $obj->getActiveSheet($key)->getstyle('A3'.':'.'AI7')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $obj->getActiveSheet($key)->getStyle('J5'.':'.'AI6')->getAlignment()->setWrapText(true);
            //列宽
            $obj->getActiveSheet($key)->getColumnDimension('E')->setWidth(30);
            $obj->getActiveSheet($key)->getColumnDimension('J')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('K')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('L')->setWidth(7);
            $obj->getActiveSheet($key)->getColumnDimension('M')->setWidth(7);
            $obj->getActiveSheet($key)->getColumnDimension('N')->setWidth(7);
            $obj->getActiveSheet($key)->getColumnDimension('O')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('P')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('Q')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('R')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('S')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('T')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('U')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('V')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('W')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('X')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('Y')->setWidth(20);
            $obj->getActiveSheet($key)->getColumnDimension('Z')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AA')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AB')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AC')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AD')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('AE')->setWidth(15);
            $obj->getActiveSheet($key)->getColumnDimension('AF')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AG')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AH')->setWidth(10);
            $obj->getActiveSheet($key)->getColumnDimension('AI')->setWidth(10);
            //行高
            $obj->getActiveSheet($key)->getRowDimension('5')->setRowHeight(70);
            $obj->getActiveSheet($key)->getRowDimension('6')->setRowHeight(30);


            //填写数据
            $data = Db::name('rddetail')
                ->alias('a')
                ->join('research_ledger b','a.rid = b.id','LEFT')
                ->field(['b.*','a.amount as total','a.type','lend_state'])
                ->where('a.pid',$v)
                ->whereRaw("DATE_FORMAT(a.Date,'%Y') = ".$year)
                ->order('b.Date,a.lend_state')
                ->select();
            $_row = 9;

            $balance = 0;$balance11 = 0;$balance12 = 0;$balance13 = 0;$balance21 = 0;$balance22 = 0;$balance23 = 0;$balance24 = 0;$balance25 = 0;$balance26 = 0;$balance27 = 0;$balance28 = 0;$balance31 = 0;
            $balance32 = 0;$balance41 = 0;$balance42 = 0;$balance43 = 0;$balance51 = 0;$balance52 = 0;$balance53 = 0;$balance54 = 0;$balance71 = 0;$balance72 = 0;
            $balance73 = 0;$balance74 = 0;$balance75 = 0;
            foreach ($data as $k=>$val){
                $obj->getActiveSheet($key)->mergeCells('A'.$_row.':'.'B'.$_row);   //合并单元格
                $obj->getActiveSheet($key)->setCellValue('A'.$_row, $val['Date']);  //设置单元格内容

                $obj->getActiveSheet($key)->mergeCells('C'.$_row.':'.'D'.$_row);   //凭证日期
                $obj->getActiveSheet($key)->setCellValue('C'.$_row, $val['Document_number']);  //凭证号

                if($val['lend_state']==2){

                    $Credit_amount = abs($val['total']);
                    $Debit_amount = 0;
                }else{
                    $Debit_amount = $val['total'];
                    $Credit_amount = 0;
                }
                $balance += (float)$val['total'];
                if($balance>0){
                    $lend_state = '借';
                }else if($balance<0){
                    $lend_state = '贷';
                }
                //  $balance = sprintf("%.0f", $balance);
                if((int)$balance==0){
                    $lend_state = '平';
                }



                $obj->getActiveSheet($key)->setCellValue('E'.$_row, $val['Abstract']);  //摘要
                $obj->getActiveSheet($key)->setCellValue('F'.$_row, $Debit_amount);  //借方金额
                $obj->getActiveSheet($key)->setCellValue('G'.$_row, $Credit_amount);  //贷方金额
                $obj->getActiveSheet($key)->setCellValue('H'.$_row, $lend_state);  //借或贷
                $obj->getActiveSheet($key)->setCellValue('I'.$_row, $balance);  //余额

                //金额类别
                switch ($val['type']){
                    case '11':
                        $balance11 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('J'.$_row, $val['total']);
                        break;
                    case '12':
                        $balance12 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('K'.$_row, $val['total']);
                        break;
                    case '13':
                        $balance13 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('L'.$_row, $val['total']);
                        break;
                    case '21':
                        $balance21 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('M'.$_row, $val['total']);
                        break;
                    case '22':
                        $balance22 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('N'.$_row, $val['total']);
                        break;
                    case '23':
                        $balance23 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('O'.$_row, $val['total']);
                        break;
                    case '24':
                        $balance24 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('P'.$_row, $val['total']);
                        break;
                    case '25':
                        $balance25 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('Q'.$_row, $val['total']);
                        break;
                    case '26':
                        $balance26 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('R'.$_row, $val['total']);
                        break;
                    case '27':
                        $balance27 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('S'.$_row, $val['total']);
                        break;
                    case '28':
                        $balance28 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('T'.$_row, $val['total']);
                        break;
                    case '31':
                        $balance31 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('U'.$_row, $val['total']);
                        break;
                    case '32':
                        $balance32 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('V'.$_row, $val['total']);
                        break;
                    case '41':
                        $balance41 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('W'.$_row, $val['total']);
                        break;
                    case '42':
                        $balance42 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('X'.$_row, $val['total']);
                        break;
                    case '43':
                        $balance43 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('Y'.$_row, $val['total']);
                        break;
                    case '51':
                        $balance51 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('Z'.$_row, $val['total']);
                        break;
                    case '52':
                        $balance52+= (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AA'.$_row, $val['total']);
                        break;
                    case '53':
                        $balance53 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AB'.$_row, $val['total']);
                        break;
                    case '54':
                        $balance54 += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AC'.$_row, $val['total']);
                        break;
                    case '71':
                        $balance71 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AD'.$_row, $val['total']);
                        break;
                    case '72':
                        $balance72 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AE'.$_row, $val['total']);
                        break;
                    case '73':
                        $balance73 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AF'.$_row, $val['total']);
                        break;
                    case '74':
                        $balance74 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AG'.$_row, $val['total']);
                        break;
                    case '75':
                        $balance75 += (float)$val['total'];
                        $obj->getActiveSheet($key)->setCellValue('AH'.$_row, $val['total']);
                        break;
                }

                $_row++;
            }
            $obj->getActiveSheet($key)->mergeCells('A'.$_row.':'.'H'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A'.$_row, '期末余额');  //设置单元格内容
            $obj->getActiveSheet($key)->getstyle('A'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $obj->getActiveSheet($key)->setCellValue('I'.$_row, $balance);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('J'.$_row, $balance11);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('K'.$_row, $balance12);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('L'.$_row, $balance13);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('M'.$_row, $balance21);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('N'.$_row, $balance22);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('O'.$_row, $balance23);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('P'.$_row, $balance24);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Q'.$_row, $balance25);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('R'.$_row, $balance26);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('S'.$_row, $balance27);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('T'.$_row, $balance28);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('U'.$_row, $balance31);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('V'.$_row, $balance32);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('W'.$_row, $balance41);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('X'.$_row, $balance42);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Y'.$_row, $balance43);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('Z'.$_row, $balance51);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AA'.$_row,$balance52);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AB'.$_row,$balance53);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AC'.$_row,$balance54);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AD'.$_row,$balance71);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AE'.$_row,$balance72);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AF'.$_row,$balance73);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AG'.$_row,$balance74);  //设置单元格内容
            $obj->getActiveSheet($key)->setCellValue('AH'.$_row,$balance75);  //设置单元格内容
            $_row++;
            $obj->getActiveSheet($key)->mergeCells('A'.$_row.':'.'D'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('A'.$_row, '主办会计：');  //设置单元格内容
            $obj->getActiveSheet($key)->getstyle('A'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('A'.$_row)->getFont()->setBold(true);
            $obj->getActiveSheet($key)->mergeCells('E'.$_row.':'.'K'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('E'.$_row, '');  //设置单元格内容
            $obj->getActiveSheet($key)->mergeCells('L'.$_row.':'.'M'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('L'.$_row, '录入人员：');  //设置单元格内容
            $obj->getActiveSheet($key)->getstyle('L'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('L'.$_row)->getFont()->setBold(true);
            $obj->getActiveSheet($key)->mergeCells('N'.$_row.':'.'X'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('X'.$_row, '');  //设置单元格内容
            $obj->getActiveSheet($key)->mergeCells('Y'.$_row.':'.'Z'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('Y'.$_row, '复核人员：');  //设置单元格内容
            $obj->getActiveSheet($key)->getstyle('Y'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $obj->getActiveSheet($key)->getstyle('Y'.$_row)->getFont()->setBold(true);
            $obj->getActiveSheet($key)->mergeCells('AA'.$_row.':'.'AI'.$_row);   //合并单元格
            $obj->getActiveSheet($key)->setCellValue('AA'.$_row, '');  //设置单元格内容
            $obj->getActiveSheet($key)->getRowDimension($_row)->setRowHeight(15);



            //设置全部边框
            $styleThinBlackBorderOutline = array(
                'borders' => array(
                    'allborders' => array( //设置全部边框
                        'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                    ),

                ),
            );
            $obj->getActiveSheet($key)->getStyle( 'A2:AI'.$_row)->applyFromArray($styleThinBlackBorderOutline);
            $obj->getActiveSheet($key)->getStyle('A2:AI'.$_row)->getFont()->setSize('9');

            $obj->getActiveSheet($key)->getStyle('F9:G'.$_row)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            $obj->getActiveSheet($key)->getStyle('I8:AI'.$_row)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);


        }

        //文件名处理
//        $fileName = $project['project_name'].'-自主研发“研发支出”辅助账';
        $fileName = '自主研发“研发支出”辅助账';
        if(!$fileName){

            $fileName = uniqid(time(),true);

        }

        $objWrite = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        $isDown = true;
        if($isDown){   //网页下载

            header('pragma:public');

            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

        $savePath = './';

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码

        $_savePath = $savePath.$_fileName.'.xlsx';

        $objWrite->save($_savePath);



        return $savePath.$fileName.'.xlsx';
    }


    /**
     * 导出研发结构明细
     * @return string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function exportJgmx(){
        $year = $this->request->param('year',2019);

        //用图片做斜线

        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $obj = new \PHPExcel();
        $obj->getDefaultStyle()->getFont()->setName('宋体');
        $obj->getActiveSheet()->setTitle('sheet1');   //设置sheet名称

        $image1 = ROOT_PATH.'/public/assets/img/line1.png';
        $image2 = ROOT_PATH.'/public/assets/img/line2.png';
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setPath($image1);
        // 设置图片的宽度

//        $objDrawing->setWidth(295);

        $objDrawing->setResizeProportional(false);
        $objDrawing->setWidthAndHeight(298,60);
        $objDrawing->setCoordinates('A4');
        $objDrawing->setWorksheet($obj->getActiveSheet());

        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setPath($image2);
        // 设置图片的宽度
        $objDrawing->setHeight(120);
        $objDrawing->setWidth(80);
        $objDrawing->setCoordinates('A4');
        $objDrawing->setWorksheet($obj->getActiveSheet());

        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        //样式
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(37);
        $obj->getActiveSheet()->getRowDimension('5')->setRowHeight(55);
        $obj->getActiveSheet()->getStyle('A1:H21')->getFont()->setSize(13);

        $obj->getActiveSheet()->setCellValue('A4', '研发项目编号');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $obj->getActiveSheet()->getstyle('A4')->getFont()->setBold(true);
        $obj->getActiveSheet()->setCellValue('A5', '                   累计发生额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A5')->getFont()->setBold(true);
        $obj->getActiveSheet()->setCellValue('A6', '科目    ');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A6')->getFont()->setBold(true);

        $obj->getActiveSheet()->setCellValue('A3', '公司名称:');  //设置合并后的单元格内容


        $obj->getActiveSheet()->setCellValue('A7', '内部研究开发费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A8', '其中：人员人工费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A9', '直接投入费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A10', '折旧费用与长期待摊费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A11', '无形资产摊销费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A12', '设计费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A13', '装备调试费用与试验费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A14', '其他费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A15', '委托外部研究开发费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A16', ' 其中：境内的外部研发费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A17', '研究开发费用(内、外部)小计');  //设置合并后的单元格内容

        $obj->getActiveSheet()->setCellValue('A19', '企业填报人签字:');  //设置合并后的单元格内容


        $data = Db::name('project')
            ->alias('a')
            ->join('rddetail b','b.pid = a.id','INNER')
            ->where('a.user_id',2)
            ->where('b.user_id',2)
            ->whereRaw("DATE_FORMAT(b.date,'%Y') = ".$year)
            ->field(['a.id','project_name','project_number'])
            ->group('b.pid')
            ->select();
        foreach ($data as $key=>$val){
            $totalA = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereNotIn('type','81,82')->field(Db::raw("SUM(amount) as total"))->find();
            $ryrgfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','11,12,13')->field(Db::raw("SUM(amount) as total"))->find();
            $zjtrfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','21,22,23,24,25,26,27,28')->field(Db::raw("SUM(amount) as total"))->find();
            $zjfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','31,32,33,34')->field(Db::raw("SUM(amount) as total"))->find();
            $wxzctxfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','41,42,43')->field(Db::raw("SUM(amount) as total"))->find();
            $xcpsjfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','51,52')->field(Db::raw("SUM(amount) as total"))->find();
            $zbfy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','53,54,61,62')->field(Db::raw("SUM(amount) as total"))->find();
            $other = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','71,72,73,74,75,76')->field(Db::raw("SUM(amount) as total"))->find();
            $wtyffy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','81,82')->field(Db::raw("SUM(amount) as total"))->find();
            $jnyffy = Db::name('rddetail')->where(['pid'=>$val['id'],'lend_state'=>1])->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)->whereIn('type','81')->field(Db::raw("SUM(amount) as total"))->find();

            $data[$key]['totalA'] = $totalA['total']==null ? '0' : round($totalA['total']/10000,2);
            $data[$key]['ryrgfy'] = $ryrgfy['total']==null ? '0' : round($ryrgfy['total']/10000,2);
            $data[$key]['zjtrfy'] = $zjtrfy['total']==null ? '0' : round($zjtrfy['total']/10000,2);
            $data[$key]['zjfy'] = $zjfy['total']==null ? '0' : round($zjfy['total']/10000,2);
            $data[$key]['wxzctxfy'] = $wxzctxfy['total']==null ? '0' : round($wxzctxfy['total']/10000,2);
            $data[$key]['xcpsjfy'] = $xcpsjfy['total']==null ? '0' : round($xcpsjfy['total']/10000,2);
            $data[$key]['zbfy'] = $zbfy['total']==null ? '0' : round($zbfy['total']/10000,2);
            $data[$key]['other'] = $other['total']==null ? '0' : round($other['total']/10000,2);
            $data[$key]['wtyffy'] = $wtyffy['total']==null ? '0' : round($wtyffy['total']/10000,2);
            $data[$key]['jnyffy'] = $jnyffy['total']==null ? '0' : round($jnyffy['total']/10000,2);
        }

        //内容
        $cellIndex = 1;
        $rowIndex = 4;
        //rows total
        $total7 = 0; $total8 = 0; $total9 = 0; $total10 = 0; $total11 = 0; $total12 = 0; $total13 = 0; $total14 = 0;  $total15 = 0; $total16 = 0; $total17 = 0; $total18 = 0;
        foreach ($data as $key=>$val){
            $obj->getActiveSheet()->getColumnDimension($cellName[$cellIndex])->setWidth(15);
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'4', $val['project_number']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->mergeCells($cellName[$cellIndex].'5'.':'.$cellName[$cellIndex].'6');
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'5', $val['project_name']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'7', $val['totalA']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'8', $val['ryrgfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'9', $val['zjtrfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'10', $val['zjfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'11', $val['xcpsjfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'12', $val['zbfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'13', $val['wxzctxfy']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'14', $val['other']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'15', $val['wtyffy']*0.8);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'16', $val['jnyffy']*0.8);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'17', (float)$val['totalA']+((float)$val['wtyffy']*0.8));  //设置合并后的单元格内容

            //计算每行总金额
            $total7 += (float)$val['totalA'];
            $total8 += (float)$val['ryrgfy'];
            $total9 += (float)$val['zjtrfy'];
            $total10 += (float)$val['zjfy'];
            $total11 += (float)$val['xcpsjfy'];
            $total12 += (float)$val['zbfy'];
            $total13 += (float)$val['wxzctxfy'];
            $total14 += (float)$val['other'];
            $total15 += (float)$val['wtyffy']*0.8;
            $total16 += (float)$val['jnyffy']*0.8;
            $total17 += (float)$val['totalA']+(float)$val['wtyffy']*0.8;

            $cellIndex++;
        }

        if(count($data)<4){
            for($i = count($data);$i < 4 ; $i++){
                $obj->getActiveSheet()->mergeCells($cellName[$i+1].'5'.':'.$cellName[$i+1].'6');
                $obj->getActiveSheet()->setCellValue($cellName[$i+1].'5', '');  //设置合并后的单元格内容
                $obj->getActiveSheet()->getColumnDimension($cellName[$i+1])->setWidth(15);
            }
            $cellIndex = 4;
        }

        $obj->getActiveSheet()->getColumnDimension($cellName[$cellIndex])->setWidth(15);
        $obj->getActiveSheet()->mergeCells($cellName[$cellIndex].'4'.':'.$cellName[$cellIndex].'6');
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'4', '合计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'7', $total7);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'8', $total8);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'9', $total9);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'10', $total10);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'11', $total11);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'12', $total12);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'13', $total13);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'14', $total14);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'15', $total15);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'16', $total16);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex].'17', $total17);  //设置合并后的单元格内容

        $obj->getActiveSheet()->getStyle('B4'.':'.$cellName[$cellIndex].'6')->getAlignment()->setWrapText(true);//超出换行
        $obj->getActiveSheet()->getStyle('B4'.':'.$cellName[$cellIndex].'6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getstyle('B4'.':'.$cellName[$cellIndex].'6')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $title = $year.'年度研究开发费用结构明细表';
        $obj->getActiveSheet()->mergeCells('A1'.':'.$cellName[$cellIndex].'1');   //合并单元格
        $obj->getActiveSheet()->setCellValue('A1', $title);  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A1')->getFont()->setBold(true);
        $obj->getActiveSheet()->getstyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $obj->getActiveSheet()->mergeCells($cellName[$cellIndex-1].'3'.':'.$cellName[$cellIndex].'3');   //合并单元格
        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex-1].'3', '单位：人民币万元');  //设置合并后的单元格内容

        $obj->getActiveSheet()->setCellValue($cellName[$cellIndex-1].'19', '日期：');  //设置合并后的单元格内容

        //锁定
        //$obj->getActiveSheet()->getStyle('A1:E19')->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $obj->getActiveSheet()->getProtection()->setSheet(true);
        $obj->getActiveSheet()->protectCells('A1:E19', 'PHPExcel');

        $obj->getActiveSheet()->getStyle('A1:E19')
            ->getProtection()
            ->setHidden(\PHPExcel_Style_Protection::PROTECTION_PROTECTED);
        $obj->getActiveSheet()->getStyle('A1:E19')
            ->getProtection()
            ->setLocked(\PHPExcel_Style_Protection::PROTECTION_PROTECTED);
        //设置全部边框
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array( //设置全部边框
                    'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                ),

            ),
        );
        $obj->getActiveSheet()->getStyle( 'A7:'.$cellName[$cellIndex].'17')->applyFromArray($styleThinBlackBorderOutline);
        $obj->getActiveSheet()->getStyle( 'B4:'.$cellName[$cellIndex].'6')->applyFromArray($styleThinBlackBorderOutline);
        //给顶部设置边框
        $styleThinBlackBorderOutlineTop = array(
            'borders' => array(
                'top'=>array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )//粗的是thick
            ),
        );
        $obj->getActiveSheet()->getStyle( 'A4')->applyFromArray($styleThinBlackBorderOutlineTop);
        //文件名处理
        $fileName = '研究开发费用结构明细表';
        if(!$fileName){

            $fileName = uniqid(time(),true);

        }

        $objWrite = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        $isDown = true;
        if($isDown){   //网页下载

            header('pragma:public');

            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

        $savePath = './';

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码

        $_savePath = $savePath.$_fileName.'.xlsx';

        $objWrite->save($_savePath);



        return $savePath.$fileName.'.xlsx';

    }

    //辅助账归集导出
    public function exportCollect()
    {
        $year = $this->request->param('year', 2017);

        $obj = new \PHPExcel();
        $obj->getDefaultStyle()->getFont()->setName('宋体');
        $obj->getActiveSheet()->setTitle('sheet1');   //设置sheet名称
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $obj->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
        $obj->getActiveSheet()->getStyle('A2:D43')->getFont()->setSize(10);

        $obj->getActiveSheet()->mergeCells('A1'.':'.'D1');   //合并单元格
        $obj->getActiveSheet()->setCellValue('A1', '研发项目可加计扣除研究开发费用情况归集表');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A1')->getFont()->setBold(true);
        $obj->getActiveSheet()->getstyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getstyle('A3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $obj->getActiveSheet()->setCellValue('A2', '纳税人名称（盖章）：');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C2', '纳税人识别号：');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('A3'.':'.'B3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('A3', $year.'年度');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D4', '金额单位：元(列至角分）');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('D4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $obj->getActiveSheet()->setCellValue('A4', '序号');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B4'.':'.'C4');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B4', '项目');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('B4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->setCellValue('D3', '金额单位：元(列至角分');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('D3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->setCellValue('D4', '发生额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('D4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $obj->getActiveSheet()->setCellValue('A5', '1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A6', '1.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A7', '1.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A8', '1.3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A9', '2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A10', '2.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A11', '2.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A12', '2.3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A13', '2.4');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A14', '2.5');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A15', '2.6');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A16', '2.7');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A17', '2.8');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A18', '3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A19', '3.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A20', '3.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A21', '4');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A22', '4.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A23', '4.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A24', '4.3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A25', '5');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A26', '5.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A27', '5.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A28', '5.3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A29', '5.4');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A30', '6');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A31', '6.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A32', '6.2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A33', '6.3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A34', '6.4');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A35', '6.5');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A36', '7');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A37', '7.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A38', '8');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A39', '8.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A40', '9');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A41', '10');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A42', '10.1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A43', '11');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A5:A43')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $obj->getActiveSheet()->mergeCells('B5'.':'.'C5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B5', '一、人员人工费用小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B6'.':'.'B7');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B6', '直接从事研发活动人员');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C6', '工资薪金');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C7', '五险一金');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B8'.':'.'C8');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B8', '外聘研发人员的劳务费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B9'.':'.'C9');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B9', '二、直接投入费用小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B10'.':'.'B12');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B10', '研发活动直接消耗');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C10', '材料');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C11', '燃料');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('C12', '动力费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B13'.':'.'C13');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B13', '用于中间试验和产品试制的模具、工艺装备开发及制造费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B14'.':'.'C14');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B14', '用于不构成固定资产的样品、样机及一般测试手段购置费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B15'.':'.'C15');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B15', '用于试制产品的检验费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B16'.':'.'C16');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B16', '用于研发活动的仪器、设备的运行维护、调整、检验、维修等费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B17'.':'.'C17');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B17', '通过经营租赁方式租入的用于研发活动的仪器、设备租赁费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B18'.':'.'C18');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B18', '三、折旧费用小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B19'.':'.'C19');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B19', '用于研发活动的仪器的折旧费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B20'.':'.'C20');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B20', '用于研发活动的设备的折旧费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B21'.':'.'C21');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B21', '四、无形资产摊销小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B22'.':'.'C22');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B22', '用于研发活动的软件的摊销费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B23'.':'.'C23');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B23', '用于研发活动的专利权的摊销费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B24'.':'.'C24');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B24', '用于研发活动的非专利技术（包括许可证、专有技术、设计和计算方法等）的摊销费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B25'.':'.'C25');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B25', '五、新产品设计费等小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B26'.':'.'C26');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B26', '新产品设计费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B27'.':'.'C27');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B27', '新工艺规程制定费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B28'.':'.'C28');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B28', '新药研制的临床试验费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B29'.':'.'C29');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B29', '勘探开发技术的现场试验费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B30'.':'.'C30');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B30', '六、其他相关费用小计');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B31'.':'.'C31');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B31', '技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B32'.':'.'C32');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B32', '研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B33'.':'.'C33');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B33', '知识产权的申请费、注册费、代理费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B34'.':'.'C34');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B34', '差旅费、会议费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B35'.':'.'C35');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B35', '职工福利费、补充养老保险费、补充医疗保险费');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B36'.':'.'C36');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B36', '七、委托外部机构或个人进行研发活动所发生的费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B37'.':'.'C37');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B37', '其中：委托境外进行研发活动所发生的费用（包括存在关联关系的委托研发）');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B38'.':'.'C38');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B38', '八、允许加计扣除的研发费用中的第1至5类费用合计（1+2+3+4+5）');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B39'.':'.'C39');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B39', '其他相关费用限额=序号8×10％/(1-10％)');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B40'.':'.'C40');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B40', '九、当期费用化支出可加计扣除总额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B41'.':'.'C41');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B41', '十、研发项目形成无形资产当期摊销额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B42'.':'.'C42');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B42', '其中：准予加计扣除的摊销额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B43'.':'.'C43');   //合并单元格
        $obj->getActiveSheet()->setCellValue('B43', '十一、当期实际加计扣除总额（9+10.1）×75％');  //设置合并后的单元格内容
        $obj->getActiveSheet()->getstyle('A5:C43')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $obj->getActiveSheet()->getstyle('A4:D43')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $obj->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B18')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B21')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B25')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B30')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B36')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B38')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B40')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B41')->getFont()->setBold(true);
        $obj->getActiveSheet()->getStyle('B43')->getFont()->setBold(true);

        //数据填充
        $data = Db::name('rddetail')
            ->where('user_id',$this->auth->id)
            ->where('lend_state',1)
            ->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)
            ->field([Db::raw("SUM(amount) as total"),'type'])
            ->group('type')
            ->select();

        $cateArr = [11=>['key'=>'Laborcosts_salary','name'=>'工资薪金'],12=>['key'=>'Laborcosts_si','name'=>'五险一金'],13=>['key'=>'Laborcosts_services','name'=>'外聘研发人员的劳务费'],
            21=>['key'=>'Directinput_materials','name'=>'材料费'],22=>['key'=>'Directinput_fuelcosts','name'=>'燃料费'],23=>['key'=>'Directinput_powercosts','name'=>'动力费用'],
            24=>['key'=>'Directinput_moldcosts','name'=>'试制模具、工艺装备费'],25=>['key'=>'Directinput_samplecosts','name'=>'样品、样机费等'],
            26=>['key'=>'Directinput_inspectioncosts','name'=>'试制产品检验费'],27=>['key'=>'Directinput_maintenancecosts','name'=>'仪器设备运维、检验费等'],
            28=>['key'=>'Directinput_rentalcosts','name'=>'仪器设备租赁费'],31=>['key'=>'Depreciation_instrument','name'=>'仪器折旧'],
            32=>['key'=>'Depreciation_equipment','name'=>'设备折旧'],33=>['key'=>'IA_amortization_software','name'=>'在用建筑物折旧'],34=>['key'=>'IA_amortization_patent','name'=>'长期待摊费用'],
            41=>['key'=>'IA_amortization_nonpatented','name'=>'软件摊销'],42=>['key'=>'Product_design','name'=>'专利权摊销'],
            43>['key'=>'Other1','name'=>'非专利技术摊销'],51=>['key'=>'Other2','name'=>'新产品设计费'],
            52=>['key'=>'Other3','name'=>'新工艺规程制定费'],53=>['key'=>'Other4','name'=>'新药研制的临床试验费'],
            54=>['key'=>'Other5','name'=>'勘探开发技术的现场试验费'],61=>['key'=>'Other5','name'=>'装备调试费用'],62=>['key'=>'Other5','name'=>'田间试验费'],
            71=>['key'=>'Other5','name'=>'技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费'],72=>['key'=>'Other5','name'=>'研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用'],73=>['key'=>'Other5','name'=>'知识产权的申请费、注册费、代理费'],
            74=>['key'=>'Other5','name'=>'差旅费、会议费'],75=>['key'=>'Other5','name'=>'职工福利费、补充养老保险费、补充医疗保险费'],76=>['key'=>'Other5','name'=>'通讯费'],
            81=>['key'=>'Other5','name'=>'委托境内研发'],82=>['key'=>'Other5','name'=>'委托境外研发']];
        $cateids = array_keys($cateArr);
        foreach ($data as $key=>$val){
            $cate[$val['type']] = $val['total'];
        }
        foreach ($cateArr as $k1=>$v1){
            if(!isset($cate[$k1])){
                $cate[$k1] = 0;
            }
        }

        //发生额
        $total5  = (float)$cate[11]+(float)$cate[12]+(float)$cate[13];
        $obj->getActiveSheet()->setCellValue('D5', $total5);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D6', (float)$cate[11]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D7', (float)$cate[12]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D8', (float)$cate[13]);  //设置合并后的单元格内容
        $total9 = (float)$cate[21]+(float)$cate[22]+(float)$cate[23]+(float)$cate[24]+(float)$cate[25]+(float)$cate[26]+(float)$cate[27]+(float)$cate[28];
        $obj->getActiveSheet()->setCellValue('D9', $total9);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D10', (float)$cate[21]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D11', (float)$cate[22]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D12', (float)$cate[23]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D13', (float)$cate[24]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D14', (float)$cate[25]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D15', (float)$cate[26]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D16', (float)$cate[27]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D17', (float)$cate[28]);  //设置合并后的单元格内容
        $total18 = (float)$cate[31]+(float)$cate[32];
        $obj->getActiveSheet()->setCellValue('D18', $total18);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D19', (float)$cate[31]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D20', (float)$cate[32]);  //设置合并后的单元格内容
        $total21 = (float)$cate[41]+(float)$cate[42]+(float)$cate[43];
        $obj->getActiveSheet()->setCellValue('D21', $total21);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D22', (float)$cate[41]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D23', (float)$cate[42]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D24', (float)$cate[43]);  //设置合并后的单元格内容
        $total25 = (float)$cate[51]+(float)$cate[52]+(float)$cate[53]+(float)$cate[54];
        $obj->getActiveSheet()->setCellValue('D25', $total25);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D26', (float)$cate[51]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D27', (float)$cate[52]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D28', (float)$cate[53]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D29', (float)$cate[54]);  //设置合并后的单元格内容
        $total30 = (float)$cate[71]+(float)$cate[72]+(float)$cate[73]+(float)$cate[74]+(float)$cate[75];
        $obj->getActiveSheet()->setCellValue('D30', $total30);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D31', (float)$cate[71]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D32', (float)$cate[72]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D33', (float)$cate[73]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D34', (float)$cate[74]);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D35', (float)$cate[75]);  //设置合并后的单元格内容
        $total36 = (float)$cate[81]*0.8+(float)$cate[82]*0.8;
        $obj->getActiveSheet()->setCellValue('D36', $total36);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D37', (float)$cate[82]*0.8);  //设置合并后的单元格内容
        $total38 = $total5+$total9+$total18+$total21+$total25;
        $obj->getActiveSheet()->setCellValue('D38', $total38);  //设置合并后的单元格内容
        $total39 = $total38*0.1/(1-0.1);
        $obj->getActiveSheet()->setCellValue('D39', round($total39,2));  //设置合并后的单元格内容
        $total40 = $total39>$total30 ? $total38+$total30 : $total38+$total39;
        $obj->getActiveSheet()->setCellValue('D40', round($total40,2));  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D41', 0);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D42', 0);  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('D43', round($total40*0.75,2));  //设置合并后的单元格内容


        //设置全部边框
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array( //设置全部边框
                    'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                ),

            ),
        );
        $obj->getActiveSheet()->getStyle( 'A4:D43')->applyFromArray($styleThinBlackBorderOutline);


        //文件名处理
        $fileName = '研发项目可加计扣除研究开发费用情况归集表';
        if(!$fileName){

            $fileName = uniqid(time(),true);

        }

        $objWrite = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        $isDown = true;
        if($isDown){   //网页下载

            header('pragma:public');

            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

        $savePath = './';

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码

        $_savePath = $savePath.$_fileName.'.xlsx';

        $objWrite->save($_savePath);



        return $savePath.$fileName.'.xlsx';
    }

    /**
     * “研发支出”辅助账汇总表 excel导出
     */
    public function exportSummary(){
        $year = $this->request->param('year',2019);

        $obj = new \PHPExcel();
        $obj->getDefaultStyle()->getFont()->setName('宋体');
        $obj->getActiveSheet()->setTitle('sheet1');   //设置sheet名称
        $obj->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);

        $obj->getActiveSheet()->mergeCells('A1:AQ1');
        $obj->getActiveSheet()->setCellValue('A1','“研发支出”辅助账汇总表');
        $obj->getActiveSheet()->getStyle('A1')->getFont()->setBold('true');
        $obj->getActiveSheet()->getStyle('A1')->getFont()->setSize('15');
        $obj->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $obj->getActiveSheet()->mergeCells('A2:C2');
        $obj->getActiveSheet()->setCellValue('A2','纳税人识别号：');
        $obj->getActiveSheet()->getStyle('A2')->getFont()->setBold('true');
        $obj->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $obj->getActiveSheet()->mergeCells('L2:N2');
        $obj->getActiveSheet()->setCellValue('L2','纳税人名称（盖章）：');
        $obj->getActiveSheet()->getStyle('L2')->getFont()->setBold('true');
        $obj->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $obj->getActiveSheet()->mergeCells('X2:AM2');
        $obj->getActiveSheet()->setCellValue('X2',$year.'年度');
        $obj->getActiveSheet()->getStyle('X2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $obj->getActiveSheet()->mergeCells('AN2:AQ2');
        $obj->getActiveSheet()->setCellValue('AN2','金额单位：元（列至角分）');
        $obj->getActiveSheet()->getStyle('AN2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $obj->getActiveSheet()->mergeCells('A3:A5');
        $obj->getActiveSheet()->setCellValue('A3','项目');
        $obj->getActiveSheet()->setCellValue('A6','行次、序号');
        $obj->getActiveSheet()->mergeCells('B3:B6');
        $obj->getActiveSheet()->setCellValue('B3','序号');
        $obj->getActiveSheet()->mergeCells('C3:C6');
        $obj->getActiveSheet()->setCellValue('C3','项目名称');
        $obj->getActiveSheet()->mergeCells('D3:D6');
        $obj->getActiveSheet()->setCellValue('D3','项目编号');
        $obj->getActiveSheet()->mergeCells('E3:E6');
        $obj->getActiveSheet()->setCellValue('E3','研发形式');
        $obj->getActiveSheet()->mergeCells('F3:F6');
        $obj->getActiveSheet()->setCellValue('F3','资本化、费用化支出选项');
        $obj->getActiveSheet()->mergeCells('G3:G6');
        $obj->getActiveSheet()->setCellValue('G3','项目实施状态选项');
        $obj->getActiveSheet()->mergeCells('H3:H6');
        $obj->getActiveSheet()->setCellValue('H3','委托方与受托方是否存在关联关系选项');
        $obj->getActiveSheet()->mergeCells('I3:I6');
        $obj->getActiveSheet()->setCellValue('I3','是否委托境外选项');
        $obj->getActiveSheet()->mergeCells('J3:J6');
        $obj->getActiveSheet()->setCellValue('J3','研发成果');
        $obj->getActiveSheet()->mergeCells('K3:K6');
        $obj->getActiveSheet()->setCellValue('K3','研发成果证书号');

        $obj->getActiveSheet()->mergeCells('L3'.':'.'N3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('L3', '一、人员人工费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('O3'.':'.'V3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('O3', '二、直接投入费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('W3'.':'.'X3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('W3', '三、折旧费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('Y3'.':'.'AA3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('Y3', '四、无形资产摊销');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AB3'.':'.'AE3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AB3', '五、新产品设计费等');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AF3'.':'.'AK3');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AF3', '六、其他相关费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('L4'.':'.'M4');   //合并单元格
        $obj->getActiveSheet()->setCellValue('L4', '直接从事研发活动人员');  //设置合并后的单元格内容

        $obj->getActiveSheet()->setCellValue('L5', '工资薪金');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('M5', '五险一金');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('N4'.':'.'N5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('N4', '外聘研发人员的劳务费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('O4'.':'.'Q4');   //合并单元格
        $obj->getActiveSheet()->setCellValue('O4', '研发活动直接消耗');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('O5', '材料');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('P5', '燃料');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('Q5', '动力费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('R4'.':'.'R5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('R4', '用于中间试验和产品试制的模具、工艺装备开发及制造费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('S4'.':'.'S5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('S4', '用于不构成固定资产的样品、样机及一般测试手段购置费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('T4'.':'.'T5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('T4', '用于试制产品的检验费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('U4'.':'.'U5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('U4', '用于研发活动的仪器、设备的运行维护、调整、检验、维修等费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('V4'.':'.'V5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('V4', '通过经营租赁方式租入的用于研发活动的仪器、设备租赁费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('W4'.':'.'W5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('W4', '用于研发活动的仪器的折旧费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('X4'.':'.'X5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('X4', '用于研发活动的设备的折旧费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('Y4'.':'.'Y5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('Y4', '用于研发活动的软件的摊销费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('Z4'.':'.'Z5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('Z4', '用于研发活动的专利权的摊销费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AA4'.':'.'AA5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AA4', '用于研发活动的非专利技术（包括许可证、专有技术、设计和计算方法等）的摊销费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AB4'.':'.'AB5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AB4', '新产品设计费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AC4'.':'.'AC5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AC4', '新工艺规程制定费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AD4'.':'.'AD5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AD4', '新药研制的临床试验费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AE4'.':'.'AE5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AE4', '勘探开发技术的现场试验费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AF4'.':'.'AF5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AF4', '技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AG4'.':'.'AG5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AG4', '研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AH4'.':'.'AH5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AH4', '知识产权的申请费、注册费、代理费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AI4'.':'.'AI5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AI4', '差旅费、会议费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AJ4'.':'.'AJ5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AJ4', '职工福利费、补充养老保险费、补充医疗保险费');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AK4'.':'.'AK5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AK4', '');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AL3'.':'.'AL5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AL3', '七、委托外部机构或个人进行研发活动所发生的费用');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AM3'.':'.'AM5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AM3', '其中：委托境外进行研发活动所发生的费用（包括存在关联关系的委托研发）');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AN3'.':'.'AN5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AN3', '八、允许加计扣除的研发费用中的第1至5类费用合计（1+2+3+4+5）');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AO3'.':'.'AO5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AO3', '其他相关费用限额=序号8×10％/(1-10％)');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AP3'.':'.'AP5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AP3', '九、当期费用化支出可加计扣除总额');  //设置合并后的单元格内容

        $obj->getActiveSheet()->mergeCells('AQ3'.':'.'AQ5');   //合并单元格
        $obj->getActiveSheet()->setCellValue('AQ3', '当期资本化可加计扣除的研发费用率');  //设置合并后的单元格内容

        $arr = ['L'=>'1.1','M'=>'1.2','N'=>'1.3','O'=>'2.1','P'=>'2.2','Q'=>'2.3','R'=>'2.4','S'=>'2.5','T'=>'2.6','U'=>'2.7','V'=>'2.8','W'=>'3.1','X'=>'3.2','Y'=>'4.1','Z'=>'4.2','AA'=>'4.3','AB'=>'5.1','AC'=>'5.2','AD'=>'5.3',
            'AE'=>'5.4','AF'=>'6.1','AG'=>'6.2','AH'=>'6.3','AI'=>'6.4','AJ'=>'6.5','AK'=>'6.6','AL'=>'7','AM'=>'7.1','AN'=>'8','AO'=>'8.1','AP'=>'9','AQ'=>'9.1'];
        foreach ($arr as $key=>$val){
            $obj->getActiveSheet()->setCellValue($key.'6', $val);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($key.'7', '0.00');  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($key.'11', '0.00');  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue($key.'12', '0.00');  //设置合并后的单元格内容
        }

        $obj->getActiveSheet()->setCellValue('A7', '1');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B7:K7');
        $obj->getActiveSheet()->setCellValue('B7', '期初余额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A8', '2');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B8:K8');
        $obj->getActiveSheet()->setCellValue('B8', '本期借方发生额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A9', '3');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B9:K9');
        $obj->getActiveSheet()->setCellValue('B9', '本期贷方发生额');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A10', '4');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B10:K10');
        $obj->getActiveSheet()->setCellValue('B10', '其中：结转管理费用');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A11', '5');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B11:K11');
        $obj->getActiveSheet()->setCellValue('B11', '结转无形资产');  //设置合并后的单元格内容
        $obj->getActiveSheet()->setCellValue('A12', '6');  //设置合并后的单元格内容
        $obj->getActiveSheet()->mergeCells('B12:K12');
        $obj->getActiveSheet()->setCellValue('B12', '期末余额');  //设置合并后的单元格内容




        //填入数据
        $data = Db::name('project')
            ->alias('a')
            ->join('rddetail b','b.pid = a.id','INNER')
            ->where('a.user_id',$this->auth->id)
//            ->where('lend_state',1)
            ->whereRaw("DATE_FORMAT(b.date,'%Y') = ".$year)
            ->field(['a.id','project_name','project_number'])
            ->group('b.pid')
            ->select();

        $_row = 13;
        $balance = 0;$balance11 = 0;$balance12 = 0;$balance13 = 0;$balance21 = 0;$balance22 = 0;$balance23 = 0;$balance24 = 0;$balance25 = 0;$balance26 = 0;$balance27 = 0;$balance28 = 0;$balance31 = 0;
        $balance32 = 0;$balance41 = 0;$balance42 = 0;$balance43 = 0;$balance51 = 0;$balance52 = 0;$balance53 = 0;$balance54 = 0;$balance71 = 0;$balance72 = 0;
        $balance73 = 0;$balance74 = 0;$balance75 = 0;$balance81 = 0;$balance82 = 0;$balancetotal = 0;$fyxe=0;$kcze=0;
        $xh = 1;

        foreach ($data as $key=>$value) {
            $obj->getActiveSheet()->setCellValue('B'.$_row, $xh);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue('C'.$_row, $value['project_name']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue('D'.$_row, $value['project_number']);  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue('E'.$_row, '自主研发');  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue('F'.$_row, '费用化支出');  //设置合并后的单元格内容
            $obj->getActiveSheet()->setCellValue('G'.$_row, '已完成');  //设置合并后的单元格内容

            $all = Db::name('rddetail')->where(['pid'=>$value['id'],'lend_state'=>1])
                ->whereRaw("DATE_FORMAT(date,'%Y') = ".$year)
                ->field([Db::raw("SUM(amount) as total"),'type'])
                ->group('type')
                ->select();
            $balancetotals = 0;
            $amount71 =0 ; $amount72 = 0; $amount73 = 0; $amount74 = 0; $amount75 = 0;

            foreach ($all as $val) {
                $balancec81 = 0; $balancec82 = 0;
                switch ($val['type']) {
                    case '11':
                        $balance11 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('L' . $_row, $val['total']);
                        break;
                    case '12':
                        $balance12 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('M' . $_row, $val['total']);
                        break;
                    case '13':
                        $balance13 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('N' . $_row, $val['total']);
                        break;
                    case '21':
                        $balance21 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('O' . $_row, $val['total']);
                        break;
                    case '22':
                        $balance22 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('P' . $_row, $val['total']);
                        break;
                    case '23':
                        $balance23 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('Q' . $_row, $val['total']);
                        break;
                    case '24':
                        $balance24 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('R' . $_row, $val['total']);
                        break;
                    case '25':
                        $balance25 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('S' . $_row, $val['total']);
                        break;
                    case '26':
                        $balance26 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('T' . $_row, $val['total']);
                        break;
                    case '27':
                        $balance27 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('U' . $_row, $val['total']);
                        break;
                    case '28':
                        $balance28 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('V' . $_row, $val['total']);
                        break;
                    case '31':
                        $balance31 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('W' . $_row, $val['total']);
                        break;
                    case '32':
                        $balance32 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('X' . $_row, $val['total']);
                        break;
                    case '41':
                        $balance41 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('Y' . $_row, $val['total']);
                        break;
                    case '42':
                        $balance42 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('Z' . $_row, $val['total']);
                        break;
                    case '43':
                        $balance43 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AA' . $_row, $val['total']);
                        break;
                    case '51':
                        $balance51 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AB' . $_row, $val['total']);
                        break;
                    case '52':
                        $balance52 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AC' . $_row, $val['total']);
                        break;
                    case '53':
                        $balance53 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AD' . $_row, $val['total']);
                        break;
                    case '54':
                        $balance54 += (float)$val['total'];
                        $balancetotals += (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AE' . $_row, $val['total']);
                        break;
                    case '71':
                        $balance71 += (float)$val['total'];
                        $amount71 = (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AF' . $_row, $val['total']);
                        break;
                    case '72':
                        $balance72 += (float)$val['total'];
                        $amount72= (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AG' . $_row, $val['total']);
                        break;
                    case '73':
                        $balance73 += (float)$val['total'];
                        $amount73 = (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AH' . $_row, $val['total']);
                        break;
                    case '74':
                        $balance74 += (float)$val['total'];
                        $amount74 = (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AI' . $_row, $val['total']);
                        break;
                    case '75':
                        $balance75 += (float)$val['total'];
                        $amount75 = (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AJ' . $_row, $val['total']);
                        break;
                    case '81':
                        //$balance81 += (float)$val['total'];
                        $balancec81 = (float)$val['total'];
                        //$obj->getActiveSheet()->setCellValue('AJ' . $_row, $val['total']);
                        break;
                    case '82':
                        $balance82 += (float)$val['total'];
                        $balancec82 = (float)$val['total'];
                        $obj->getActiveSheet()->setCellValue('AM' . $_row, $val['total']);
                        break;
                    default:
                        break;
                }

                $balancect81 = $balancec81+$balancec82;
                $balance81 += $balancect81;
                if((int)$balancect81!==0){
                    $obj->getActiveSheet()->setCellValue('AL' . $_row, $balancect81);
                }

                // $balancetotals 38
                $obj->getActiveSheet()->setCellValue('AN' . $_row, $balancetotals);
                $fyxes = round($balancetotals*0.1/0.9,2);//39
                $obj->getActiveSheet()->setCellValue('AO' . $_row, $fyxes);

                $othertotals = (float)$amount71+(float)$amount72+(float)$amount73+(float)$amount74+(float)$amount75;

                if($fyxes>$othertotals){
                    $kczes = (float)$balancetotals+(float)$othertotals+($balancect81*0.8);
                }else{
                    $kczes = (float)$balancetotals+(float)$fyxes+($balancect81*0.8);
                }
                $obj->getActiveSheet()->setCellValue('AP' . $_row, $kczes);
                $obj->getActiveSheet()->setCellValue('AQ' . $_row, '0.00');
            }


            $balancetotal += $balancetotals;
            $fyxe += $fyxes;
            $kcze += $kczes;
            $_row++;
            $xh++;
        }

        //本期借方发生额
        $obj->getActiveSheet()->setCellValue('L8', Number_format($balance11, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('M8', Number_format($balance12, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('N8', Number_format($balance13, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('O8', Number_format($balance21, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('P8', Number_format($balance22, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Q8', Number_format($balance23, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('R8', Number_format($balance24, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('S8', Number_format($balance25, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('T8', Number_format($balance26, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('U8', Number_format($balance27, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('V8', Number_format($balance28, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('W8', Number_format($balance31, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('X8', Number_format($balance32, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Y8', Number_format($balance41, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Z8', Number_format($balance42, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AA8',Number_format($balance43, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AB8',Number_format($balance51, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AC8',Number_format($balance52, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AD8',Number_format($balance53, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AE8',Number_format($balance54, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AF8',Number_format($balance71, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AG8',Number_format($balance72, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AH8',Number_format($balance73, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AI8',Number_format($balance74, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AJ8',Number_format($balance75, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AK8',Number_format(0, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AL8',Number_format($balance81, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AM8',Number_format($balance82, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AN8',Number_format($balancetotal, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AO8',Number_format($fyxe, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AP8',Number_format($kcze, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AQ8','0.00');
        //   本期贷方发生额
        $obj->getActiveSheet()->setCellValue('L9', Number_format($balance11, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('M9', Number_format($balance12, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('N9', Number_format($balance13, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('O9', Number_format($balance21, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('P9', Number_format($balance22, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Q9', Number_format($balance23, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('R9', Number_format($balance24, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('S9', Number_format($balance25, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('T9', Number_format($balance26, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('U9', Number_format($balance27, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('V9', Number_format($balance28, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('W9', Number_format($balance31, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('X9', Number_format($balance32, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Y9', Number_format($balance41, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Z9', Number_format($balance42, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AA9',Number_format($balance43, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AB9',Number_format($balance51, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AC9',Number_format($balance52, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AD9',Number_format($balance53, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AE9',Number_format($balance54, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AF9',Number_format($balance71, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AG9',Number_format($balance72, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AH9',Number_format($balance73, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AI9',Number_format($balance74, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AJ9',Number_format($balance75, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AK9',Number_format(0, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AL9',Number_format($balance81, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AM9',Number_format($balance82, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AN9',Number_format($balancetotal, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AO9',Number_format($fyxe, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AP9',Number_format($kcze, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AQ9','0.00');
        //其中：结转管理费用
        $obj->getActiveSheet()->setCellValue('L10', Number_format($balance11, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('M10', Number_format($balance12, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('N10', Number_format($balance13, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('O10', Number_format($balance21, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('P10', Number_format($balance22, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Q10', Number_format($balance23, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('R10', Number_format($balance24, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('S10', Number_format($balance25, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('T10', Number_format($balance26, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('U10', Number_format($balance27, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('V10', Number_format($balance28, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('W10', Number_format($balance31, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('X10', Number_format($balance32, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Y10', Number_format($balance41, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('Z10', Number_format($balance42, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AA10',Number_format($balance43, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AB10',Number_format($balance51, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AC10',Number_format($balance52, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AD10',Number_format($balance53, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AE10',Number_format($balance54, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AF10',Number_format($balance71, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AG10',Number_format($balance72, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AH10',Number_format($balance73, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AI10',Number_format($balance74, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AJ10',Number_format($balance75, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AK10',Number_format(0, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AL10',Number_format($balance81, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AM10',Number_format($balance82, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AN10',Number_format($balancetotal, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AO10',Number_format($fyxe, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AP10',Number_format($kcze, 2, '.',''));
        $obj->getActiveSheet()->setCellValue('AQ10','0.00');

        $obj->getActiveSheet()->getStyle('L7:AQ'.$_row)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

        $xm = $_row-1;
        $obj->getActiveSheet()->mergeCells('A13:A'.$xm);
        $obj->getActiveSheet()->setCellValue('A13','项目明细（填写项目贷方发生额）');

        $last = $_row;
        $obj->getActiveSheet()->mergeCells('A'.$last.':C'.$last);
        $obj->getActiveSheet()->setCellValue('A'.$last,'法定代表人（签章）：');
        $obj->getActiveSheet()->getStyle('A'.$last)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $obj->getActiveSheet()->mergeCells('D'.$last.':R'.$last);
        $obj->getActiveSheet()->mergeCells('V'.$last.':AQ'.$last);
        $obj->getActiveSheet()->mergeCells('S'.$last.':U'.$last);
        $obj->getActiveSheet()->setCellValue('S'.$last,'财务负责人：');
        $obj->getActiveSheet()->getStyle('S'.$last)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        //设置全部边框
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array( //设置全部边框
                    'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                ),

            ),
        );
        $obj->getActiveSheet()->getStyle( 'A2:AQ'.$last)->applyFromArray($styleThinBlackBorderOutline);
        $obj->getActiveSheet()->getStyle( 'A2:AQ'.$last)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getStyle('A2:AQ'.$last)->getAlignment()->setWrapText(true);
        $obj->getActiveSheet()->getStyle('A2:AQ'.$last)->getFont()->setSize(9);

        $obj->getActiveSheet()->getStyle( 'A7:A12')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getStyle( 'L7:AQ6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getStyle( 'A3:AQ6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getStyle( 'B13:B'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth('10');
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth('15');
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth('7');
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth('7');
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth('7');
        $obj->getActiveSheet()->getColumnDimension('L')->setWidth('10');
        $obj->getActiveSheet()->getColumnDimension('M')->setWidth('10');
        $obj->getActiveSheet()->getColumnDimension('N')->setWidth('5');
        $obj->getActiveSheet()->getColumnDimension('O')->setWidth('10');
        $obj->getActiveSheet()->getColumnDimension('P')->setWidth('7');
        $obj->getActiveSheet()->getColumnDimension('Q')->setWidth('5');
        $obj->getActiveSheet()->getColumnDimension('U')->setWidth('10');
        $obj->getActiveSheet()->getColumnDimension('V')->setWidth('30');
        $obj->getActiveSheet()->getColumnDimension('X')->setWidth('10');

        $obj->getActiveSheet()->getRowDimension('5')->setRowHeight('100');
        //文件名处理
        $fileName = '“研发支出”辅助账汇总表';
        if(!$fileName){

            $fileName = uniqid(time(),true);

        }

        $objWrite = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        $isDown = true;
        if($isDown){   //网页下载

            header('pragma:public');

            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

        $savePath = ROOT_PATH.DS.'public'.DS;

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码

        $_savePath = $savePath.$_fileName.'.xls';

        $objWrite->save($_savePath);



        return $savePath.$fileName.'.xlsx';

    }


    /**  研究开发管理制度  **/

    /*
     * 客户列表
     */
    public function systemlist(){
        $limit = $this->request->param('limit',10);
        $data = Db::name('research_system')
            ->where('user_id',$this->auth->id)
            ->order('id', 'asc')
            ->paginate($limit);
        $items = $data->items();

        $yyzd = Db::name('cms_archives')
            ->alias('a')
            ->join('cms_addonyyzd b','a.id = b.id')
            ->where('model_id',7)
            ->field(['a.id','a.title'])
            ->select();

        $this->assign('items',$items);
        $this->assign('data',$data);
        $this->assign('yyzd',$yyzd);
        $this->view->assign('title', '研发制度');
        return $this->fetch();
    }


    public function dcphpword(){
        $dctype = $this->request->param('dctype','word');
        $type = $this->request->param('type');
        $category = $this->request->param('category');

        $hash = Db::name('cms_userwords')->where(['type'=>$type,'category'=>$category,'user_id'=>$this->auth->id])->find();

        $dateArr = [
            1=>['title'=>'研究开发组织管理制度','key'=>'Date_management'],
            2=>['title'=>'研发投入核算管理制度','key'=>'Date_accounting'],
            3=>['title'=>'研发部门成立文件','key'=>'Date_depart'],
            4=>['title'=>'科技成果转化管理办法','key'=>'Date_Achievements'],
            5=>['title'=>'科技成果转化奖励制度','key'=>'Date_reward'],
            6=>['title'=>'企业创新奖励制度','key'=>'Date_Innovation'],
            7=>['title'=>'科研人员培养进修及技能培训管理办法','key'=>'Date_training'],
            8=>['title'=>'优秀科研人才引进及绩效评价奖励管理办法','key'=>'Date_talent_introduction'],
            9=>['title'=>'科研人员绩效考核及奖励制度','key'=>'Date_performance']
        ];

        $dateArr = [
            '研究开发组织管理制度'=>'Date_management',
            '研发投入核算管理制度'=>'Date_accounting',
            '研发部门成立文件'=>'Date_depart',
            '科技成果转化管理办法'=>'Date_Achievements',
            '科技成果转化奖励制度'=>'Date_reward',
            '企业创新奖励制度'=>'Date_Innovation',
            '科研人员培养进修及技能培训管理办法'=>'Date_training',
            '优秀科研人才引进及绩效评价奖励管理办法'=>'Date_talent_introduction',
            '科研人员绩效考核及奖励制度'=>'Date_performance'
        ];

        if($hash){
            //如果不是最新生成的word，删除原本的word生成最新的word文档
            $id = $hash['id'];
            if($hash['is_new']==0){

                unlink($hash['file_path'].DS.$hash['file_name']);

                $company = Db::name('research_system')->where('user_id',$this->auth->id)->find();
                $title = $category;
                $bzrqs = $company[$dateArr[$category]];
                $bzrq = date('Y年m月d日',strtotime($bzrqs));
                $sxrq = date('Y年m月01日',strtotime('+1 month',strtotime($bzrqs)));
                $img_url = 'http://'.$_SERVER['HTTP_HOST'].$company['Company_logo'];

                $templateProcessor = new TemplateProcessor('doc/'.$title.'.docx');
                $templateProcessor->setValue('company', $company['company2']);
                $templateProcessor->setValue('title', $title);
                $templateProcessor->setValue('bzrq', $bzrq);
                $templateProcessor->setValue('sxrq', $sxrq);
                if(!empty($company['Company_logo'])){
                    $templateProcessor->setImageValue('image', ['path'=>$img_url,'width'=>'80','height'=>'40']);
                }else{
                    $templateProcessor->setValue('image', '');
                }

                $file_url = ROOT_PATH.'public'.DS.'doc/user'.$this->auth->id;
                $file_name = uniqid().'.docx';
                if(!file_exists($file_url)){
                    mkdir($file_url,0777,true);
                    chmod($file_url,0777);
                }else{
                    chmod($file_url,0777);
                }
                $templateProcessor->saveAs($file_url.DS.$file_name);
                Db::name('cms_userwords')->where(['type'=>$type,'category'=>$title])->update(['file_path'=>$file_url,'file_name'=>$file_name,'is_new'=>1]);
            }else if($hash['is_new']==1){
                if($dctype=='word'){
                    $this->download($hash['file_path'].DS.$hash['file_name']);
                }else if($dctype=='pdf'){
                    $token = uniqid();
                    cache('dctoken',$token);
//                    $url = 'http://'.$_SERVER['HTTP_HOST'].DS.'doc/user'.$this->auth->id.DS.$hash['file_name'];
                    $url = 'http://'.$_SERVER['HTTP_HOST'].DS.'index/cms.rdsystem/dcpdf?id='.$id.'&token='.$token;
                    $url = urlencode($url);
                    $word2pdf = 'http://ow365.cn/?i=18439&info=2&furl='.$url;
                    header("location:".$word2pdf);
                }
                exit;
            }
        }else{

            $company = Db::name('research_system')->where('user_id',$this->auth->id)->find();

            $title = $category;
            $bzrqs = $company[$dateArr[$category]];
            $bzrq = date('Y年m月d日',strtotime($bzrqs));
            $sxrq = date('Y年m月01日',strtotime('+1 month',strtotime($bzrqs)));

            $img_url = 'http://'.$_SERVER['HTTP_HOST'].$company['Company_logo'];

            $templateProcessor = new TemplateProcessor('doc/'.$title.'.docx');
            $templateProcessor->setValue('company', $company['company2']);
            $templateProcessor->setValue('title', $title);
            $templateProcessor->setValue('bzrq', $bzrq);
            $templateProcessor->setValue('sxrq', $sxrq);
            if(!empty($company['Company_logo'])){
                $templateProcessor->setImageValue('image', ['path'=>$img_url,'width'=>'80','height'=>'40']);
            }else{
                $templateProcessor->setValue('image', '');
            }

            $file_url = 'doc/user'.$this->auth->id;
            $file_name = uniqid().'.docx';
            if(!file_exists($file_url)){
                    mkdir($file_url,0777,true);
                    chmod($file_url,0777);
            }else{
                    chmod($file_url,0777);
            }
            $templateProcessor->saveAs($file_url.DS.$file_name);

            $data = [
                'user_id'=>$this->auth->id,
                'type'=>$type,
                'category'=>$category,
                'file_path'=>$file_url,
                'file_name'=>$file_name,
                'is_new'=>1,
                'create_time'=>time(),
                'update_time'=>time()
            ];
            $id = Db::name('cms_userwords')->insertGetId($data);
        }

        //下载文件
        if($dctype=='word'){
            $this->download(ROOT_PATH.'public'.DS.$file_url.DS.$file_name);
        }else if($dctype=='pdf'){
            $token = uniqid();
            cache('dctoken',$token);
//            $url = 'http://'.$_SERVER['HTTP_HOST'].DS.'doc/user'.$this->auth->id.DS.$hash['file_name'];
            $url = 'http://'.$_SERVER['HTTP_HOST'].DS.'index/cms.rdsystem/dcpdf?id='.$id.'&token='.$token;
            $url = urlencode($url);
            $word2pdf = 'http://ow365.cn/?i=18439&info=2&furl='.$url;
            header("location:".$word2pdf);

        }

    }
    /**
     * 导出制度PDF
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function dcpdf(){
        $id = $this->request->param('id');
        $token = $this->request->param('token');
//        return 'dctoken='.Session::get('dctoken');exit;
        if(empty($id)){
            $this->error("参数错误！");
        }
        if(cache('dctoken')!=$token){
            $this->error("非法请求！");
        }

        $file = Db::name('cms_userwords')->where('id',$id)
//            ->where('user_id',$this->auth->id)
            ->find();

        if(empty($file)){
            $this->error("文件不存在！");
        }
        cache('dctoen',uniqid());
        $this->download(ROOT_PATH.'public'.DS.$file['file_path'].DS.$file['file_name']);

    }


}