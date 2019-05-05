<?php

namespace addons\ask\controller;

use app\common\model\Signin;

class Index extends Base
{

    protected $layout = 'default';

    /**
     * 首页
     */
    public function index()
    {
        $type = $this->request->request('type', 'new');

        $questionList = \addons\ask\model\Question::getIndexQuestionList($type);
        $this->view->assign('questionList', $questionList);
        $this->view->assign('questionType', $type);
        if ($this->request->isAjax()) {
            return $this->view->fetch('ajax/get_question_list');
        }

        $this->view->assign('keywords', '');
        $this->view->assign('description', '');
        return $this->view->fetch();
    }

}
