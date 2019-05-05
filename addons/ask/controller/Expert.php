<?php

namespace addons\ask\controller;

use addons\ask\model\Certification;

class Expert extends Base
{

    protected $layout = 'default';
    protected $noNeedLogin = ['index'];

    public function index()
    {
        $expertList = \addons\ask\model\User::alias('b')
            ->join("user u", "b.user_id=u.id")
            ->where('b.isexpert', 1)
            ->order('u.score', 'desc')
            ->paginate(20);
        $this->view->assign('title', "专家");
        $this->view->assign("expertList", $expertList);
        return $this->view->fetch();
    }

    /**
     * 申请认证
     */
    public function create()
    {
        if ($this->request->isPost()) {
            $user = $this->auth->getUser();
            if ($user->ask->isexpert) {
                $this->error("当前你已经是专家，无需要重复认证");
            }
            $qq = $this->request->request('qq');
            $ability = $this->request->request('ability');
            $intro = $this->request->request('intro');
            $works = $this->request->request('works');
            if (!$qq || !$ability || !$intro || !$works) {
                $this->error("QQ号、能力、个人介绍、作品集不能为空");
            }
            $ip = $this->request->ip();
            $lastReport = Certification::where([
                'status'  => 'hidden',
                'user_id' => $this->auth->id,
            ])->find();

            if ($lastReport) {
                $this->error("已经存在正在申请的认证，请等待反馈");
            }
            $data = [
                'user_id' => $this->auth->id,
                'qq'      => $qq,
                'ability' => $ability,
                'intro'   => $intro,
                'works'   => $works,
                'ip'      => $ip,
            ];
            Certification::create($data);
            $this->success("申请认证审核，请等待反馈");
        }
        return;
    }
}
