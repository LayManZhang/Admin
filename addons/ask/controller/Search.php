<?php

namespace addons\ask\controller;

/**
 * 搜索控制器
 * Class Search
 * @package addons\ask\controller
 */
class Search extends Base
{

    protected $layout = 'default';

    public function index()
    {
        $module = $this->request->request("module", "question");
        $type = $this->request->request("type", "new");
        $keyword = $this->request->request("keyword", "");

        if ($module == 'question') {
            $searchList = \addons\ask\model\Question::getIndexQuestionList($type, null, null, null, $keyword);
        } else {
            $searchList = \addons\ask\model\Article::getIndexArticleList($type, null, null, null, $keyword);
        }

        $this->view->assign("title", $keyword);
        $this->view->assign("module", $module);
        $this->view->assign("keyword", $keyword);
        $this->view->assign("type", $type);
        $this->view->assign("searchList", $searchList);

        if ($this->request->isAjax()) {
            return $this->view->fetch('ajax/get_' . $module . '_list');
        }
        return $this->view->fetch();
    }

}
