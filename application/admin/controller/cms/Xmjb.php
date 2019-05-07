<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;
use think\Request;

/**
 * 项目级别
 *
 * @icon fa fa-circle-o
 */
class Xmjb extends Backend
{
    
    /**
     * Xmjb模型对象
     * @var \app\admin\model\cms\Xmjb
     */
    protected $model = null;
    protected $noNeedRight = ['get_sjxm'];
    protected $noNeedLogin = ['get_sjxm'];
    protected $searchFields = 'id,grade_name,grade_tname,grade_fname';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Xmjb;
        $this->view->assign("gradeTypeList", $this->model->getGradeTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function index()
    {
        if ($this->request->isAjax())
        {
            $this->relationSearch = TRUE;
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->alias('xmjb')
                ->field(['xmjb.*','xmjbb.grade_name'])
                ->join('cms_xmjb xmjbb','xmjb.parent_id = xmjbb.id','LEFT')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->alias('xmjb')
                ->field(['xmjb.*','xmjbb.grade_name as parent_name'])
                ->join('cms_xmjb xmjbb','xmjb.parent_id = xmjbb.id','LEFT')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
//            foreach ($list as $k=>$val){
//                $val['parent_name'] = db('cms_xmjb')->where('id',$val['parent_id'])->find()['grade_name'];
//            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /**
     * 获取上级项目级别
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_sjxm(Request $request){
        $post = $request->post();
        $name = isset($post['q_word']) ? $post['q_word'] : '';//搜索关键词，多个以数组形式
        $id = isset($post['searchValue']) ? $post['searchValue'] : '';//id查询
        $page = isset($post['pageNumber']) ? $post['pageNumber'] : 1;//当前页
        $limit = isset($post['pageSize']) ? $post['pageSize'] : 10;//限制条数
        $where = [];
        if(isset($id)&&!empty($id)&&$id!==0){  //id查询时，用于编辑时展示数据库数据
            $where['id'] = $post['searchValue'];
        }else if(isset($name)&&!empty($name)){//名称查询时，默认返回一二级项目级别
            $where['grade_type']= ['IN',[1,2,3]];
            foreach ($name as $k=>$v){
                $where['grade_name'] = ['LIKE','%'.$v.'%'];
            }
        }

        $offset=$limit*(round(($page-1)>=0?$page-1:1));
        $total = $this->model
            ->where($where)
            ->order('grade_type', 'asc')
            ->count();

        $list = $this->model
            ->where($where)
            ->field(['id', 'grade_fname as name','grade_type as type'])
            ->order('grade_type', 'asc')
            ->limit($offset, $limit)
            ->select();
        $result = array("total" => $total, "rows" => $list);

        return json($result);
    }

    public function get_type(){
        $id = $this->request->param('id');
        $res = $this->model->where('id',$id)->find();
        return json($res);
    }

}
