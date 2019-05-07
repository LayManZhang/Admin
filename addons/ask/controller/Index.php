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

        //签到
        $info = get_addon_info('signin');
        if ($info && $info['state']) {
            $signin = \addons\signin\model\Signin::where('user_id', $this->auth->id)->whereTime('createtime', 'today')->find();
            $this->view->assign('signin', $signin);
            $this->view->assign('signinconfig', get_addon_config('signin'));
        }
        $this->view->assign('keywords', '');
        $this->view->assign('description', '');
        return $this->view->fetch();
    }

}
