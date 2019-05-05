<?php

namespace addons\ask\model;

use app\common\library\Auth;
use think\Cache;
use think\Db;
use think\Model;

/**
 * 邀请模型
 */
class Invite Extends Model
{

    protected $name = "ask_invite";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];
    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('ask');
        self::$config = $config;
        self::afterInsert(function ($row) {
            User::increase('invites', 1, $row->invite_user_id);
        });
    }

    public function getQuestionUrlAttr($value, $data)
    {
        return addon_url("ask/question/show", [':id' => $data['question_id']]);
    }

    public static function settle($id)
    {
        $user_id = Auth::instance()->id;
        $list = self::where(['invite_user_id' => $user_id, 'question_id' => $id, 'isanswered' => 0])->select();
        foreach ($list as $index => $item) {
            $item->isanswered = 1;
            $item->save();
            if ($item->price > 0) {
                \app\common\model\User::money($item->price, $user_id, "邀请回答赏金");
            }
        }
    }

    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id')->setEagerlyType(1);
    }

    public function question()
    {
        return $this->belongsTo('\addons\ask\model\Question', 'question_id', 'id')->setEagerlyType(1);
    }

    public function invite()
    {
        return $this->belongsTo('\app\common\model\User', 'invite_user_id', 'id')->setEagerlyType(1);
    }

}
