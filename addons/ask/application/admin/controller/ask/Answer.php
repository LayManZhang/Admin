<?php

namespace app\admin\controller\ask;

use app\common\controller\Backend;

/**
 * 问答回答管理
 *
 * @icon fa fa-circle-o
 */
class Answer extends Backend
{

    /**
     * Answer模型对象
     * @var \app\admin\model\ask\Answer
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\ask\Answer;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model->with(['user', 'question'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->with(['user', 'question'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $index => $item) {
                $item->user->visible(['id', 'nickname']);
                $item->question->visible(['id', 'title']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 还原
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
        $count = 0;
        $list = $this->model->onlyTrashed()->select();
        foreach ($list as $index => $item) {
            $item->deletetime = null;
            $item->save();
            $count++;
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

}
