<?php

namespace app\admin\model\ask;

use addons\ask\library\Askdown;
use addons\ask\model\Taggable;
use addons\ask\model\User;
use addons\pay\library\Service;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

class Article extends Model
{
    use SoftDelete;
    // 表名
    protected $name = 'ask_article';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected $auto = ['content_fmt'];

    // 追加属性
    protected $append = [
        'flag_text',
        'deletetime_text',
        'status_text'
    ];
    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('ask');
        self::$config = $config;

        self::afterDelete(function ($row) use ($config) {
            $data = $row->where('id', $row->id)->find();
            if (!$data) {
                //删除相关评论
                $commentList = Comment::where('type', 'article')->where('source_id', $row['id'])->select();
                foreach ($commentList as $index => $item) {
                    $item->delete();
                }
                User::decrease('articles', 1, $row->user_id);
                //减少积分
                $config['score']['postarticle'] && \app\common\model\User::score(-$config['score']['postarticle'], $row->user_id, '删除文章');
            } else {
                //删除相关评论
                $commentList = Comment::withTrashed()->where('type', 'article')->where('source_id', $row['id'])->select();
                foreach ($commentList as $index => $item) {
                    $item->delete(true);
                }
                //删除相关标签
                $tagableList = Taggable::where('type', 'article')->where('source_id', $row['id'])->select();
                foreach ($tagableList as $index => $item) {
                    $item->delete();
                }
            }
        });

        self::afterUpdate(function ($row) use ($config) {
            $changedData = $row->getChangedData();
            if (isset($changedData['deletetime']) && is_null($changedData['deletetime'])) {
                User::increase('articles', 1, $row->user_id);
                $config['score']['postarticle'] && \app\common\model\User::score($config['score']['postarticle'], $row->user_id, '恢复文章');
            }
        });
    }

    public function setContentFmtAttr($data)
    {
        $content = Askdown::instance()->format($this->data['content']);
        return $content;
    }

    public function getFlagList()
    {
        return ['index' => __('Index'), 'hot' => __('Hot'), 'recommend' => __('Recommend'), 'top' => __('Top')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getFlagTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['flag']) ? $data['flag'] : '');
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }


    public function getDeletetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['deletetime']) ? $data['deletetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setFlagAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setDeletetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function user()
    {
        return $this->belongsTo("\app\common\model\User", 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function category()
    {
        return $this->belongsTo("Category", 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
