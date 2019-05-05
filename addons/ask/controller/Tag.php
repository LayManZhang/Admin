<?php

namespace addons\ask\controller;

use addons\ask\library\Service;
use think\Db;
use think\Exception;

class Tag extends Base
{

    protected $noNeedLogin = ['index', 'show'];
    protected $layout = 'default';

    /**
     * 话题首页
     */
    public function index()
    {
        $categoryId = $this->request->request('category');

        $tagList = \addons\ask\model\Tag::where('status', 'normal')
            ->where(function ($query) use ($categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
            })
            ->paginate(20);

        $categoryList = \addons\ask\model\Category::getIndexCategoryList('tag');

        //渲染当前已关注
        $followedTagIds = [];
        if ($this->auth->isLogin()) {
            $tagIds = [];
            foreach ($tagList as $index => $item) {
                $tagIds[] = $item['id'];
            }
            $followedTagIds = \addons\ask\model\Attention::where('user_id', $this->auth->id)
                ->where('type', 'tag')
                ->whereIn('source_id', $tagIds)
                ->column('source_id');
        }
        foreach ($tagList as $index => $item) {
            $item->followed = in_array($item['id'], $followedTagIds);
        }
        $this->view->assign('title', "话题");
        $this->view->assign("categoryId", $categoryId);
        $this->view->assign("categoryList", $categoryList);
        $this->view->assign("tagList", $tagList);

        return $this->view->fetch();
    }

    /**
     * 话题详情
     */
    public function show()
    {
        $type = $this->request->request('type', 'question');
        $id = $this->request->param('id', 0);
        $name = $this->request->param('name', '');

        $tag = $id ? \addons\ask\model\Tag::get($id) : \addons\ask\model\Tag::getByName($name);
        if (!$tag) {
            $this->error("未找到指定话题");
        }
        if ($type == 'question') {
            $questionList = \addons\ask\model\Question::getIndexQuestionList('new', null, null, $tag->id);
            $this->view->assign('questionList', $questionList);
        } else {
            $articleList = \addons\ask\model\Article::getIndexArticleList('new', null, null, $tag->id);
            $this->view->assign('articleList', $articleList);

        }
        $this->view->assign('tagType', $type);
        $this->view->assign('__tag__', $tag);
        $this->view->assign('title', $tag->name);

        if ($this->request->isAjax()) {
            if ($type == 'question') {
                return $this->view->fetch('ajax/get_question_list');
            } else {
                return $this->view->fetch('ajax/get_article_list');
            }
        }
        return $this->view->fetch();
    }

    /**
     * 编辑话题
     */
    public function update()
    {
        $this->view->engine->layout(false);
        $id = $this->request->request('id');
        $tag = \addons\ask\model\Tag::get($id);
        if (!$tag) {
            $this->error("话题未找到");
        }
        if (!Service::isAdmin()) {
            $this->error("无法进行越权操作");
        }
        if ($this->request->isPost()) {
            $name = $this->request->request("name");
            $icon = $this->request->request("icon");
            $image = $this->request->request("image");
            $intro = $this->request->request("intro");
            if (!$name) {
                $this->error("话题名称不能为空");
            }
            $data = [
                'name'  => $name,
                'icon'  => $icon,
                'image' => $image,
                'intro' => $intro,
            ];
            $tag->save($data);
            $this->success("更新成功");
        }
        $this->view->assign('__tag__', $tag);
        $this->success('', '', $this->view->fetch('tag/update'));
    }

    /**
     * 删除
     */
    public function delete()
    {
        $tag_id = $this->request->param('id');

        $tag = \addons\ask\model\Tag::get($tag_id);
        if (!$tag) {
            $this->error("话题未找到!");
        }
        if ($tag['deletetime']) {
            $this->error("话题已删除!");
        }
        if (!Service::isAdmin()) {
            $this->error("无法进行越权访问!");
        }

        Db::startTrans();
        try {
            $tag->delete();
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->success("删除失败");
        }
        $this->success("删除成功", addon_url("ask/tag/index"));
    }

}
