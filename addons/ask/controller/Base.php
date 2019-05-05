<?php

namespace addons\ask\controller;

use addons\ask\library\Service;
use think\Config;
use think\Validate;

/**
 * 问答控制器基类
 */
class Base extends \think\addons\Controller
{

    // 初始化
    public function __construct()
    {
        parent::__construct();

        $config = get_addon_config('ask');
        // 加载自定义标签库
        $this->view->engine->config('taglib_pre_load', 'addons\ask\taglib\Ask');
        // 定义CMS首页的URL
        $config['indexurl'] = addon_url('ask/index/index', [], false);
        Config::set('ask', $config);

        if (Config::get('upload.uploadurl') == 'ajax/upload') {
            Config::set('upload.uploadurl', url('index/ajax/upload'));
        }
        Config::set('paginate.query', $this->request->get());
        $url_domain_deploy = $config['domain'] && Config::get('url_domain_deploy');
        $askConfig = array_merge(['upload' => Config::get("upload"), 'controllername' => $this->controller, 'actionname' => $this->action, 'url_domain_deploy' => $url_domain_deploy], Config::get("view_replace_str"));
        $askConfig['loadmode'] = $config['loadmode'];
        $askConfig['pagesize'] = $config['pagesize'];
        $askConfig['inviteprice'] = $config['inviteprice'];
        $askConfig['user'] = $this->auth->isLogin() ? array_intersect_key($this->auth->getUserinfo(), array_flip(['id', 'username', 'nickname', 'avatar', 'score', 'money'])) : null;
        $this->view->assign('askConfig', $askConfig);
    }

    public function _initialize()
    {
        parent::_initialize();
        //如果登录的情况下
        if ($this->auth->isLogin()) {
            $user = $this->auth->getUser();
            $user->ask = \addons\ask\model\User::get($this->auth->id);
            $this->view->assign('user', $user);
        }
        $this->view->assign('isAdmin', Service::isAdmin());
        // 如果请求参数action的值为一个方法名,则直接调用
        $action = $this->request->post("action");
        if ($action && $this->request->isPost()) {
            return $this->$action();
        }
    }

    protected function token()
    {
        $token = $this->request->post('__token__');

        //验证Token
        if (!Validate::is($token, "token", ['__token__' => $token])) {
            $this->error("Token验证错误，请重试！", '', ['__token__' => $this->request->token()]);
        }

        //刷新Token
        $this->request->token();
    }

}
