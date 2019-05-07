<?php

namespace app\admin\controller\cms;

use app\admin\model\cms\Channel;
use app\admin\model\cms\ChannelAdmin;
use app\admin\model\cms\Modelx;
use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\db\Query;
use think\Request;

/**
 * 内容表
 *
 * @icon fa fa-circle-o
 */
class Archives extends Backend
{

    /**
     * Archives模型对象
     */
    protected $model = null;
    protected $noNeedLogin = ['get_xmjb','get_xmjbxq','format'];
    protected $noNeedRight = ['get_channel_fields', 'check_element_available','get_xmjb','get_xmjbxq','format'];
    protected $isSuperAdmin = false;
    protected $searchFields = 'id,title';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Archives;


        //是否超级管理员
        $this->isSuperAdmin = $this->auth->isSuperAdmin();
        $channelList = [];
        $disabledIds = [];
        $all = collection(Channel::order("weigh desc,id desc")->select())->toArray();

        //允许的栏目
        $this->channelIds = $this->isSuperAdmin ? Channel::column('id') : ChannelAdmin::getAdminChanneIds();
        $parentChannelIds = Channel::where('id', 'in', $this->channelIds)->column('parent_id');
        foreach ($all as $k => $v) {
            $state = ['opened' => true];
            if ($v['type'] != 'list') {
                $disabledIds[] = $v['id'];
            }
            if ($v['type'] == 'link') {
                $state['checkbox_disabled'] = true;
            }
            if (!$this->isSuperAdmin) {
                if (($v['type'] != 'list' && !in_array($v['id'], $parentChannelIds)) || ($v['type'] == 'list' && !in_array($v['id'], $this->channelIds))) {
                    unset($all[$k]);
                    continue;
                }
            }
            $channelList[] = [
                'id'     => $v['id'],
                'parent' => $v['parent_id'] ? $v['parent_id'] : '#',
                'text'   => __($v['name']),
                'type'   => $v['type'],
                'state'  => $state
            ];
        }

        $tree = Tree::instance()->init($all, 'parent_id');
        $channelOptions = $tree->getTree(0, "<option value=@id @selected @disabled>@spacer@name</option>", '', $disabledIds);
        $this->view->assign('channelOptions', $channelOptions);
        $this->assignconfig('channelList', $channelList);

        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("statusList", $this->model->getStatusList());

        $cms = get_addon_config('cms');
        $this->assignconfig('cms', ['archiveseditmode' => $cms['archiveseditmode']]);

    }
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->relationSearch = TRUE;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if (!$this->auth->isSuperAdmin()) {
                $this->model->where('channel_id', 'in', $this->channelIds);
            }
            $total = $this->model
                ->with('Channel')
                ->where($where)
                ->order($sort, $order)
                ->count();
            if (!$this->auth->isSuperAdmin()) {
                $this->model->where('channel_id', 'in', $this->channelIds);
            }
            $list = $this->model
                ->with('Channel')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        $modelList = \app\admin\model\cms\Modelx::all();
        $this->view->assign('modelList', $modelList);
        return $this->view->fetch();
    }

    /**
     * 副表内容
     */
    public function content($model_id = null)
    {
        $model = \app\admin\model\cms\Modelx::get($model_id);
        if (!$model) {
            $this->error('未找到对应模型');
        }
        $fieldsList = \app\admin\model\cms\Fields::where('model_id', $model['id'])->where('type', '<>', 'text')->select();

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $fields = [];
            foreach ($fieldsList as $index => $item) {
                $fields[] = "addon." . $item['name'];
            }
            $table = $this->model->getTable();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $sort = 'main.id';
            $isSuperAdmin = $this->isSuperAdmin;
            $channelIds = $this->channelIds;
            $customWhere = function ($query) use ($isSuperAdmin, $channelIds, $model_id) {
                if (!$isSuperAdmin) {
                    $query->where('main.channel_id', 'in', $channelIds);
                }
                if ($model_id) {
                    $query->where('main.model_id', $model_id);
                }
            };
            $total = Db::table($table)
                ->alias('main')
                ->join('cms_channel channel', 'channel.id=main.channel_id', 'LEFT')
                ->join($model['table'] . ' addon', 'addon.id=main.id', 'LEFT')
                ->field('main.id,main.channel_id,main.title,channel.name as channel_name,addon.id as aid' . ($fields ? ',' . implode(',', $fields) : ''))
                ->where($customWhere)
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = Db::table($table)
                ->alias('main')
                ->join('cms_channel channel', 'channel.id=main.channel_id', 'LEFT')
                ->join($model['table'] . ' addon', 'addon.id=main.id', 'LEFT')
                ->field('main.id,main.channel_id,main.title,channel.name as channel_name,addon.id as aid' . ($fields ? ',' . implode(',', $fields) : ''))
                ->where($customWhere)
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);
            //var_dump($list);exit;
            return json($result);


        }
        $fields = [];

        //项目级别id转换
        $xmjb = db('cms_xmjb')->field(['id','grade_name as name'])->select();
        $xmjb_arr = [];
        foreach ($xmjb as $item){
            $xmjb_arr[$item['id']] = $item['name'];
        }

        //所属项目id转换
        $ssxm = db('cms_archives')->field(['id','title as name'])->where('channel_id',49)->select();
        $ssxm_arr = [];
        foreach ($ssxm as $item){
            $ssxm_arr[$item['id']] = $item['name'];
        }

        foreach ($fieldsList as $index => $item) {
            if($item['name']=='xmjb'){
                $fields[] = ['field' => $item['name'], 'title' => $item['title'], 'type' => $item['type'], 'content' => $xmjb_arr];
            } else if($item['name']=='ssxm'){
                $fields[] = ['field' => $item['name'], 'title' => $item['title'], 'type' => $item['type'], 'content' => $ssxm_arr];
            }else{
                $fields[] = ['field' => $item['name'], 'title' => $item['title'], 'type' => $item['type'], 'content' => $item['content_list']];
            }
        }
        $this->assignconfig('fields', $fields);
        $this->view->assign('fieldsList', $fieldsList);
        $this->view->assign('model', $model);
        $this->assignconfig('model_id', $model_id);
        $modelList = \app\admin\model\cms\Modelx::all();
        $this->view->assign('modelList', $modelList);
        return $this->view->fetch();
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
//
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $channel_id = $params['channel_id'];
                        switch ($channel_id){//区分栏目导入不同的附表
                            case 48:
                                break;
                            case 49:
                                break;
                            case 51:
                                $id = $this->model->id;
                                $file = $params['drgsmd'];
                                $this->up_excel($id,$file);
                                break;
                            case 52:
                                break;
                            case 58:
                                break;
                            case 60:
                                break;
                            default:
                                break;
                        }
                        return $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     *
     * @param mixed $ids
     * @return string
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if (!$this->isSuperAdmin && !in_array($row['channel_id'], $this->channelIds)) {
            $this->error(__('You have no permission'));
        }
        if ($this->request->isPost()) {
            $channel_id = $this->request->post('row/a')['channel_id'];
            switch ($channel_id){
                case 51://项目公示
                    //判断文件是否导入过，已导入则不操作
                    $file = $this->request->post('row/a')['drgsmd'];
                    if(!empty($file)){
                        $xmgsk = new \app\admin\model\cms\Addonxmgsk();
                        $gsk = $xmgsk->where('ssxm',$ids)->find();
                        if($file!==$gsk['filepath']){
                            $this->up_excel($ids,$file);
                        }
                    }
                    break;
                default:
                    break;
            }

            return parent::edit($ids);
        }


        $channel = Channel::get($row['channel_id']);
        if (!$channel) {
            $this->error(__('No specified channel found'));
        }
        $model = \app\admin\model\cms\Modelx::get($channel['model_id']);
        if (!$model) {
            $this->error(__('No specified model found'));
        }
        $addon = db($model['table'])->where('id', $row['id'])->find();
        if ($addon) {
            $row = array_merge($row->toArray(), $addon);
        }

        $disabledIds = [];
        $all = collection(Channel::order("weigh desc,id desc")->select())->toArray();
        foreach ($all as $k => $v) {
            if ($v['type'] != 'list' || $v['model_id'] != $channel['model_id']) {
                $disabledIds[] = $v['id'];
            }
        }
        $tree = Tree::instance()->init($all, 'parent_id');
        $channelOptions = $tree->getTree(0, "<option value=@id @selected @disabled>@spacer@name</option>", $row['channel_id'], $disabledIds);
        $this->view->assign('channelOptions', $channelOptions);
        $this->view->assign("row", $row);
        $this->assignconfig('row', $row);//传递给js，用于系列（联动）数据赋值
        return $this->view->fetch();
    }

    public function up_excel($ids='',$file=''){
        $xmgsk = new \app\admin\model\cms\Addonxmgsk();
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
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

        $table = $xmgsk->getQuery()->getTable();
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
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);

            foreach ($temp as $k => $v) {
                if (isset($fieldArr[$k]) && $k !== '') {
                    $row[$fieldArr[$k]] = $v;
                    $row['ssxm'] = $ids;
                    $row['content'] = '';
                    $row['filepath'] = $file;
                }
            }

            if ($row) {
                if($row['project_name']!==''){
                    $insert[] = $row;
                }
            }
        }

        if (!$insert) {
            $this->error(__('No rows were updated'));
        }
        //判断是否导入过，导入过则清空，重新导入
        $check = $xmgsk->where('ssxm',$ids)->find();
        if($check){
            $xmgsk->where('ssxm',$ids)->delete();
        }
        $xmgsk->saveAll($insert);
//        $this->success();


    }

    /**
     * 删除
     * @param mixed $ids
     */
    public function del($ids = "")
    {
        \app\admin\model\cms\Archives::event('after_delete', function ($row) {
            Channel::where('id', $row['channel_id'])->where('items', '>', 0)->setDec('items');
        });
        parent::del($ids);
    }

    /**
     * 销毁
     * @param string $ids
     */
    public function destroy($ids = "")
    {
        \app\admin\model\cms\Archives::event('after_delete', function ($row) {
            //删除副表
            $channel = Channel::get($row->channel_id);
            if ($channel) {
                $model = Modelx::get($channel['model_id']);
                if ($model) {
                    db($model['table'])->where("id", $row['id'])->delete();
                }
            }
        });
        parent::destroy($ids);
    }

    /**
     * 还原
     * @param mixed $ids
     */
    public function restore($ids = "")
    {
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $archivesChannelIds = $this->model->onlyTrashed()->column('id,channel_id');
        $archivesChannelIds = array_filter($archivesChannelIds);
        $this->model->where('id', 'in', array_keys($archivesChannelIds));
        $count = $this->model->restore('1=1');
        if ($count) {
            $channelNums = array_count_values($archivesChannelIds);
            foreach ($channelNums as $k => $v) {
                Channel::where('id', $k)->setInc('items', $v);
            }
            $this->success();
        }
        $this->error(__('No rows were updated'));

    }

    /**
     * 移动
     * @param string $ids
     */
    public function move($ids = "")
    {
        if ($ids) {
            $channel_id = $this->request->post('channel_id');
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $this->model->where($pk, 'in', $ids);
            $channel = Channel::get($channel_id);
            if ($channel && $channel['type'] === 'list') {
                $channelNums = \app\admin\model\cms\Archives::
                with('channel')
                    ->where('archives.' . $pk, 'in', $ids)
                    ->where('channel_id', '<>', $channel['id'])
                    ->field('channel_id,COUNT(*) AS nums')
                    ->group('channel_id')
                    ->select();
                $result = $this->model
                    ->where('model_id', '=', $channel['model_id'])
                    ->where('channel_id', '<>', $channel['id'])
                    ->update(['channel_id' => $channel_id]);
                if ($result) {
                    $count = 0;
                    foreach ($channelNums as $k => $v) {
                        if ($v['channel']) {
                            Channel::where('id', $v['channel_id'])->where('items', '>', 0)->setDec('items', min($v['channel']['items'], $v['nums']));
                        }
                        $count += $v['nums'];
                    }
                    Channel::where('id', $channel_id)->setInc('items', $count);
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            } else {
                $this->error(__('No rows were updated'));
            }
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
    }

    /**
     * 获取栏目列表
     * @internal
     */
    public function get_channel_fields()
    {
        $this->view->engine->layout(false);
        $channel_id = $this->request->post('channel_id');
        $archives_id = $this->request->post('archives_id');
        $channel = Channel::get($channel_id, 'model');
        if ($channel && $channel['type'] === 'list') {

            $values = [];
            if ($archives_id) {
                $values = db($channel['model']['table'])->where('id', $archives_id)->find();
            }

            $fields = \app\admin\model\cms\Fields::where('model_id', $channel['model_id'])
                ->where('status','normal')//筛选出正常的字段，隐藏的字段不显示
                ->order('weigh desc,id desc')
                ->select();
            foreach ($fields as $k => $v) {
                //优先取编辑的值,再次取默认值
                $v->value = isset($values[$v['name']]) ? $values[$v['name']] : (is_null($v['defaultvalue']) ? '' : $v['defaultvalue']);
                $v->rule = str_replace(',', '; ', $v->rule);
                if (in_array($v->type, ['checkbox', 'lists', 'images'])) {
                    $checked = '';
                    if ($v['minimum'] && $v['maximum'])
                        $checked = "{$v['minimum']}~{$v['maximum']}";
                    else if ($v['minimum'])
                        $checked = "{$v['minimum']}~";
                    else if ($v['maximum'])
                        $checked = "~{$v['maximum']}";
                    if ($checked)
                        $v->rule .= (';checked(' . $checked . ')');
                }
                if (in_array($v->type, ['checkbox', 'radio']) && stripos($v->rule, 'required') !== false) {
                    $v->rule = str_replace('required', 'checked', $v->rule);
                }
                if (in_array($v->type, ['selects'])) {
                    $v->extend .= (' ' . 'data-max-options="' . $v['maximum'] . '"');
                }
            }

            $this->view->assign('fields', $fields);
            $this->view->assign('values', $values);
//            if($channel_id==100){
//                $this->success('', null, ['html' => $this->view->fetch('fields2')]);
//            }
            $this->success('', null, ['html' => $this->view->fetch('fields')]);
        } else {
            $this->error(__('Please select channel'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 检测元素是否可用
     * @internal
     */
    public function check_element_available()
    {
        $id = $this->request->request('id');
        $name = $this->request->request('name');
        $value = $this->request->request('value');
        $name = substr($name, 4, -1);
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', 'name'));
        }
        if ($id) {
            $this->model->where('id', '<>', $id);
        }
        $exist = $this->model->where($name, $value)->find();
        if ($exist) {
            $this->error(__('The data already exist'));
        } else {
            $this->success();
        }
    }

    /**
     * 获取项目级别（用于添加项目项目级别三级联动）
     * @return false|string
     */
    public function get_xmjb(){
        $id = input('param.id');
        if(isset($id)){
            $data = db('cms_xmjb')->where('parent_id',$id)->field(['id','grade_name as name'])->select();
        }else{
            $data = db('cms_xmjb')->where('grade_type',1)->field(['id','grade_name as name'])->select();;
        }
        return json_encode($data);
    }


    /**
     * 获取项目项目级别详情（项目公示编辑显示）
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_xmjbxq(){
        $id = $city = input('param.id');
        $data = db('cms_addonxmgs')->alias('xmgs')->join('cms_archives archives','archives.id = xmgs.ssxm')->where('xmgs.id',$id)->field(['xmgs.*','archives.title'])->find();
        return json_encode($data);
    }

    /**
     * 获取项目指南项目级别
     * @return false|string
     */
    public function get_xmjbznxq(){
        $id = $city = input('param.id');
        $data = db('cms_addonxmzn')->where('id',$id)->field(['xmjb'])->find();
        return json_encode($data);
    }



    /**
     * 项目公示时获取项目指南属性
     * @param Request $request
     * @return \think\response\Json
     */
    public function get_xmzn(Request $request){

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $post = $request->post();
        $name = isset($post['q_word']) ? $post['q_word'] : '';//搜索关键词，多个以数组形式
        $id = isset($post['searchValue']) ? $post['searchValue'] : '';//id查询
        $page = isset($post['pageNumber']) ? $post['pageNumber'] : 1;//当前页
        $limit = isset($post['pageSize']) ? $post['pageSize'] : 10;//限制条数
        $where = [];
        if(isset($id)&&!empty($id)&&$id!==0){  //id查询时，用于编辑时展示数据库数据
            $where['archives.id'] = $post['searchValue'];
            $where['channel_id'] = 49;
        }else if(isset($name)&&!empty($name)){//名称查询时，默认返回一二级项目级别
            $where['channel_id'] = 49;
            foreach ($name as $k=>$v){
                $where['title'] = ['LIKE','%'.$v.'%'];
            }
        }

        $offset=$limit*(round(($page-1)>=0?$page-1:1));
        $total = db('cms_archives')
            ->alias('archives')
            ->where($where)
            ->order('id', 'asc')
            ->count();
        $list = db('cms_archives')
            ->alias('archives')
            ->field(['archives.id','title as name','xmzn.id as xmznid','xmzn.*'])
            ->join('cms_addonxmzn xmzn','xmzn.id = archives.id')
            ->where($where)
            ->order('archives.id', 'asc')
            ->limit($offset, $limit)
            ->select();

        $result = array("total" => $total, "rows" => $list);

        return json($result);
    }


    /**
     * 获取项目指南详细信息
     * @param $id
     * @return false|string
     */
    public function get_xmzninfo($id){
        //根据文章id ；联表查询fields字段表，查出name,type,title
        $data = db('cms_archives')
            ->alias('archives')
            ->join('cms_addonxmzn xmzn','xmzn.id = archives.id','LEFT')//取字段值
            ->join('cms_fields fields','fields.model_id = archives.model_id','LEFT')//取字段类型
            ->where('archives.id',$id)
            ->field(['fields.name as keyname','fields.type','fields.title','xmzn.*'])
            ->select();
        $res = [];
        foreach ($data as $key=>$val){
            $res[$key]['keyname'] = $val['keyname'];
            $res[$key]['keyvalue'] = $val[$val['keyname']];
            $res[$key]['type'] = $val['type'];
            $res[$key]['title'] = $val['title'];
        }
        return json_encode($res);
    }


}
