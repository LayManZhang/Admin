<?php

namespace addons\ask\model;

use think\Model;

/**
 * 消息模型
 */
class Message Extends Model
{

    protected $name = "ask_message";
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
            User::increase('messages', 1, $row->to_user_id);
        });
    }

    public function from()
    {
        return $this->belongsTo('\app\common\model\User', 'from_user_id', 'id')->setEagerlyType(1);
    }

    public function to()
    {
        return $this->belongsTo('\app\common\model\User', 'to_user_id', 'id')->setEagerlyType(1);
    }

}
