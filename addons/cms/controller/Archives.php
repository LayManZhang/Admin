<?php

namespace addons\cms\controller;

use addons\cms\model\Archives as ArchivesModel;
use addons\cms\model\Channel;
use addons\cms\model\Modelx;
use think\Config;
use think\Db;

/**
 * 文档控制器
 * Class Archives
 * @package addons\cms\controller
 */
class Archives extends Base
{

    public function index()
    {
        $action = $this->request->post("action");
        if ($action && $this->request->isPost()) {
            return $this->$action();
        }
        $diyname = $this->request->param('diyname');
        if ($diyname && !is_numeric($diyname)) {
            $archives = ArchivesModel::getByDiyname($diyname);
        } else {
            $id = $diyname ? $diyname : $this->request->param('id', '');
            $archives = ArchivesModel::get($id, ['channel']);
        }
        if (!$archives || ($archives['user_id'] != $this->auth->id && $archives['status'] != 'normal') || $archives['deletetime']) {
            $this->error(__('No specified article found'));
        }
        $channel = Channel::get($archives['channel_id']);
        if (!$channel) {
            $this->error(__('No specified channel found'));
        }
        $model = Modelx::get($channel['model_id'], [], true);
        if (!$model) {
            $this->error(__('No specified model found'));
        }
        $addon = db($model['table'])->where('id', $archives['id'])->find();
        if ($addon) {
            if ($model->fields) {
                $fieldsContentList = $model->getFieldsContentList($model->id);
                //附加列表字段
                array_walk($fieldsContentList, function ($content, $field) use (&$addon) {
                    if(isset($addon[$field])){
                        $object = explode(',',$addon[$field]);
                        $object_text = '';
                        foreach ($object as $k=>$v){
//                        $addon[$field . '_text'] = isset($content[$addon[$v]]) ? $content[$addon[$v]] : $addon[$field];
                            $object_text .= isset($content[$v]) ? $content[$v].' ' : $addon[$field].'';
                            $addon[$field . '_text'] = $object_text;
                        }
                    }
                    if(isset($addon['xmjb'])){
                        $object = explode(',',$addon['xmjb']);
                        $object = array_filter($object);
                        $length = count($object);
                        $xmjb = $object[$length-2];
                        $object_text = Db::name('cms_xmjb')->where('id',$xmjb)->find()['grade_fname'];
                        $addon['xmjb_text'] = $object_text;
                    }
                });

            }
            $archives->setData($addon);
        } else {
            $this->error('No specified addon article found');
        }
        $archives->setInc("views", 1);

        $this->view->assign("__ARCHIVES__", $archives);

        $this->view->assign("__CHANNEL__", $channel);
        Config::set('cms.title', $archives['title']);
        Config::set('cms.keywords', $archives['keywords']);
        Config::set('cms.description', $archives['description']);
        $template = preg_replace('/\.html$/', '', $channel['showtpl']);
        return $this->view->fetch('/' . $template);
    }


    /**
     * 赞与踩
     */
    public function vote()
    {
        $id = (int)$this->request->post("id");
        $type = trim($this->request->post("type", ""));
        if (!$id || !$type) {
            $this->error(__('Operation failed'));
        }
        $archives = ArchivesModel::get($id);
        if (!$archives || ($archives['user_id'] != $this->auth->id && $archives['status'] != 'normal') || $archives['deletetime']) {
            $this->error(__('No specified article found'));
        }
        $archives->where('id', $id)->setInc($type === 'like' ? 'likes' : 'dislikes', 1);
        $archives = ArchivesModel::get($id);
        $this->success(__('Operation completed'), null, ['likes' => $archives->likes, 'dislikes' => $archives->dislikes, 'likeratio' => $archives->likeratio]);
    }

}
