<?php

namespace addons\ask\controller;

use addons\ask\library\Askdown;
use addons\ask\library\Service;
use addons\ask\library\VicDict;
use think\Config;
use think\Db;

/**
 * 导入
 * Class Import
 * @package addons\ask\controller
 */
class Import extends Base
{
    protected $noNeedLogin = ["*"];
    protected $layout = 'default';

    public function _initialize()
    {
        parent::_initialize();

        if (!$this->request->isCli()) {
            $this->error('只允许在终端进行操作!');
        }
    }

    /**
     * 导入修罗社区
     */
    public function xiuno()
    {
        $config = Config::get('database');
        $config['database'] = 'fastadmin-forum';
        $config['prefix'] = 'bbs_';

        //提问数据
        Db::connect($config)->name("thread")->alias("t")
            ->join("bbs_post p", "p.tid=t.tid", "right")
            ->where("p.isfirst", 1)
            ->field("p.tid,t.tid AS id,t.fid AS category_id,t.subject AS title,t.uid AS user_id,t.create_date AS createtime,t.last_date AS updatetime,t.views,t.posts AS answers,p.message AS content")
            ->chunk(100, function ($items) {
                foreach ($items as $index => &$item) {
                    unset($item['tid']);
                    $item['content_fmt'] = Askdown::instance()->format($item['content']);
                    $item['status'] = 'normal';
                }
                unset($item);
                Db::name('ask_question')->insertAll($items);
            }, "t.tid");

        //回答数据
        Db::connect($config)->name("post")->where("isfirst", 0)->field("pid,pid AS id,tid AS question_id,isbest,message AS content,create_date AS createtime,create_date AS updatetime,likes AS voteup,thanks,uid AS user_id")->chunk(100, function ($items) {

            foreach ($items as $index => &$item) {
                $item['adopttime'] = null;
                if ($item['isbest']) {
                    Db::name('ask_question')->where('id', $item['question_id'])->update(['best_answer_id' => $item['id']]);
                    $item['adopttime'] = $item['createtime'];
                }
                unset($item['isbest']);
                unset($item['pid']);
                $item['content_fmt'] = Askdown::instance()->format($item['content']);
                $item['status'] = 'normal';
            }
            unset($item);
            Db::name('ask_answer')->insertAll($items);
        });

        //会员基础数据
        Db::name("user")->field("id,id AS user_id")->chunk(1000, function ($items) {
            foreach ($items as $index => &$item) {
                unset($item['id']);
            }
            Db::name("ask_user")->insertAll($items);
        });

        //导入感谢
        /*
        Db::connect($config)->name("post_thanks")->chunk(100, function ($items) use ($config) {

            $list = [];
            foreach ($items as $index => &$item) {
                $post = Db::connect($config)->name("post")->where('pid', $item['pid'])->find();
                if ($post) {
                    $list[] = [
                        'user_id'    => $item['uid'],
                        'type'       => $post['isfirst'] ? 'question' : 'answer',
                        'source_id'  => $item['pid'],
                        'money'      => $item['money'],
                        'createtime' => $item['createtime'],
                        'updatetime' => $item['createtime'],
                        'status'     => 'paid',
                    ];
                }
            }
            unset($item);
            Db::name('ask_thanks')->insertAll($list);
        });
        */

        //点赞
        $likeList = Db::connect($config)->name("post_like")->select();
        foreach ($likeList as $index => $item) {
            $post = Db::connect($config)->name("post")->where('pid', $item['pid'])->find();
            if ($post) {
                $data = [
                    'user_id'    => $item['uid'],
                    'type'       => $post['isfirst'] ? 'question' : 'answer',
                    'source_id'  => $item['pid'],
                    'ip'         => $item['create_ip'],
                    'createtime' => $item['create_date'],
                    'updatetime' => $item['create_date'],
                ];
                \addons\ask\model\Vote::create($data);
                $model = Service::getModelByType($data['type'], $data['source_id']);
                if ($model) {
                    $model->setInc('voteup');
                }
            }
        }

        //收藏
        Db::connect($config)->name("haya_favorite")
            ->field("tid,uid,create_date")
            ->chunk(100, function ($items) {
                foreach ($items as $index => &$item) {
                    $question = \addons\ask\model\Collection::where(['type' => 'question', 'source_id' => $item['tid'], 'user_id' => $item['uid']])->find();
                    if ($question) {
                        unset($items[$index]);
                        continue;
                    }
                    $item['type'] = 'question';
                    $item['source_id'] = $item['tid'];
                    $item['user_id'] = $item['uid'];
                    $item['createtime'] = $item['create_date'];
                    unset($item['tid'], $item['uid'], $item['create_date']);
                }
                unset($item);
                Db::name('ask_collection')->insertAll($items);
            }, "tid");

        //统计数量
        $userList = \addons\ask\model\User::where("user_id", "in", function ($query) {
            $query->name("ask_question")->field("user_id");
        })->whereOr("user_id", "in", function ($query) {
            $query->name("ask_answer")->field("user_id");
        })->select();
        foreach ($userList as $index => $item) {
            $item->questions = \addons\ask\model\Question::where('user_id', $item->user_id)->count();
            $item->answers = \addons\ask\model\Answer::where('user_id', $item->user_id)->count();
            $item->save();
        }
        $userList = \addons\ask\model\User::where("user_id", "in", function ($query) {
            $query->name("ask_answer")->where('adopttime', '>', 0)->field("user_id");
        })->select();
        foreach ($userList as $index => $item) {
            $item->adoptions = \addons\ask\model\Answer::where('user_id', $item->user_id)->where('adopttime', '>', 0)->count();
            $item->save();
        }
        echo "done";

    }

    /**
     * 导入词典
     */
    public function dict()
    {
        define('_VIC_WORD_DICT_PATH_', ADDON_PATH . 'ask/data/dict.json');
        $dict = new VicDict('json');

        //添加词语词库 add(词语,词性) 可以是除保留字符（/，\ ， \x  ，\i），以外的utf-8编码的任何字符
        $lines = file(ADDON_PATH . 'ask/data/dict.txt', FILE_IGNORE_NEW_LINES);
        foreach ($lines as $index => $line) {
            $lineArr = explode(' ', $line);
            $dict->add($lineArr[0], 'n');
        }

        //保存词库
        $dict->save();
        echo "done";
    }

    public function question()
    {
        \think\Db::execute("UPDATE fa_ask_user SET unadopted=0");
        $list = \addons\ask\model\Question::where('best_answer_id', 0)->field("COUNT(*) AS nums,user_id")->group("user_id")->select();
        foreach ($list as $index => $item) {
            (new \addons\ask\model\User())->where('user_id', $item['user_id'])->update(['unadopted' => $item['nums']]);
        }

        $list = \addons\ask\model\Article::withTrashed()->select();
        foreach ($list as $index => $item) {
            $item->comments = \addons\ask\model\Comment::where('type', 'article')->where('source_id', $item['id'])->count();
            $item->save();
        }

        //重新统计评论、提问、文章
        $list = \addons\ask\model\Comment::field('COUNT(*) AS nums,user_id')->group("user_id")->having("nums>0")->select();
        foreach ($list as $index => $item) {
            (new \addons\ask\model\User())->where('user_id', $item['user_id'])->update(['comments' => $item['nums']]);
        }
        $list = \addons\ask\model\Article::field('COUNT(*) AS nums,user_id')->group("user_id")->having("nums>0")->select();
        foreach ($list as $index => $item) {
            (new \addons\ask\model\User())->where('user_id', $item['user_id'])->update(['articles' => $item['nums']]);
        }
        $list = \addons\ask\model\Question::field('COUNT(*) AS nums,user_id')->group("user_id")->having("nums>0")->select();
        foreach ($list as $index => $item) {
            (new \addons\ask\model\User())->where('user_id', $item['user_id'])->update(['questions' => $item['nums']]);
        }
        $list = \addons\ask\model\Answer::field('COUNT(*) AS nums,user_id')->group("user_id")->having("nums>0")->select();
        foreach ($list as $index => $item) {
            (new \addons\ask\model\User())->where('user_id', $item['user_id'])->update(['answers' => $item['nums']]);
        }
        echo "done";
    }

    public function format()
    {
        $askdown = Askdown::instance();
        echo "question";
        $questionList = \addons\ask\model\Question::withTrashed()->select();
        foreach ($questionList as $index => $item) {
            $item->content = str_replace(["&lt;", "&gt;"], ["<", ">"], $item['content']);
            $item->save();
        }
        echo "article";
        $questionList = \addons\ask\model\Article::withTrashed()->select();
        foreach ($questionList as $index => $item) {
            $item->content = str_replace(["&lt;", "&gt;"], ["<", ">"], $item['content']);
            $item->content_fmt = $askdown->format($item);
            $item->save();
        }

        echo "answer";
        $questionList = \addons\ask\model\Answer::withTrashed()->select();
        foreach ($questionList as $index => $item) {
            $item->content = str_replace(["&lt;", "&gt;"], ["<", ">"], $item['content']);
            $item->content_fmt = $askdown->format($item);
            $item->save();
        }

        echo "comment";
        $questionList = \addons\ask\model\Comment::withTrashed()->select();
        foreach ($questionList as $index => $item) {
            $item->content = str_replace(["&lt;", "&gt;"], ["<", ">"], $item['content']);
            $item->content_fmt = $askdown->format($item);
            $item->save();
        }

        echo "done";
        return;
    }
}
