<?php

namespace addons\ask\model;

use addons\ask\library\Askdown;
use app\common\library\Auth;
use fast\Date;
use think\Cache;
use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

/**
 * 问题模型
 */
class Question extends Model
{
    protected $name = "ask_question";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected $auto = ['content_fmt'];

    protected $type = [
        'price' => 'float'
    ];

    // 追加属性
    protected $append = [
        'url',
        'fullurl',
        'create_date',
    ];
    protected static $config = [];

    use SoftDelete;

    protected static function init()
    {
        $config = get_addon_config('ask');
        self::$config = $config;
        //发送@消息
        self::afterInsert(function ($row) use ($config) {
            Askdown::instance()->notification('question', $row->id);
            User::increase('questions', 1, $row->user_id);
            User::increase('unadopted', 1, $row->user_id);
            //增加积分
            $config['score']['postquestion'] && \app\common\model\User::score($config['score']['postquestion'], $row->user_id, '发布问题');
        });
        self::afterDelete(function ($row) use ($config) {
            //删除相关回答
            $answerList = Answer::where('question_id', $row['id'])->select();
            foreach ($answerList as $index => $item) {
                $item->delete();
            }
            //删除相关评论
            $commentList = Comment::where('type', 'question')->where('source_id', $row['id'])->select();
            foreach ($commentList as $index => $item) {
                $item->delete();
            }
            User::decrease('questions', 1, $row->user_id);
            if (!$row['best_answer_id']) {
                User::decrease('unadopted', 1, $row->user_id);
            }
            //悬赏退回
            if ($row->price > 0 && !$row->best_answer_id) {
                \app\common\model\User::money($row->price, $row->user_id, '悬赏退回');
                $row->price = 0;
                $row->rewardtime = 0;
                $row->save();
            }
            //减少积分
            $config['score']['postquestion'] && \app\common\model\User::score(-$config['score']['postquestion'], $row->user_id, '删除问题');
        });
        self::afterUpdate(function ($row) {
            $changedData = $row->getChangedData();
            //增减未采纳数
            if (isset($changedData['best_answer_id'])) {
                if ($changedData['best_answer_id']) {
                    User::decrease('unadopted', 1, $row->user_id);
                } else {
                    User::increase('unadopted', 1, $row->user_id);
                }
            }
            if (isset($changedData['status'])) {
                //关闭问题时悬赏退回
                if ($changedData['status'] == 'closed') {
                    //减少未采纳数
                    if (!$row['best_answer_id']) {
                        User::decrease('unadopted', 1, $row->user_id);
                    }
                    //退回赏金
                    if ($row->price > 0 && !$row->best_answer_id) {
                        \app\common\model\User::money($row->price, $row->user_id, '悬赏退回');
                        $row->price = 0;
                        $row->rewardtime = 0;
                        $row->save();
                    }
                } elseif ($changedData['status'] == 'normal') {
                    //增加未采纳数
                    if (!$row['best_answer_id']) {
                        User::increase('unadopted', 1, $row->user_id);
                    }
                }
            }
        });
    }

    /**
     * 批量设置数据
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setTagData($data)
    {
        $this->data['tags'][] = $data;
    }

    public function getContentOutputAttr($value, $data)
    {
        return str_replace(["<", ">"], ["&lt;", "&gt;"], $data['content']);
    }

    public function setContentFmtAttr($data)
    {
        $content = Askdown::instance()->format($this->data['content']);
        return $content;
    }

    public function getCreateDateAttr($value, $data)
    {
        return time() - $data['createtime'] > 7 * 86400 ? date("Y-m-d", $data['createtime']) : human_date($data['createtime']);
    }

    public function getImageAttr($value, $data)
    {
        $value = $value ? $value : self::$config['default_question_image'];
        return cdnurl($value, true);
    }

    public function getTagsAttr($value, $data)
    {
        if (isset($this->data['tags'])) {
            return $this->data['tags'];
        }
        $tags = Tag::getTags('question', $data['id']);
        return $tags;
    }

    public function getTagsTextAttr($value, $data)
    {
        if (isset($this->data['tags_text'])) {
            return $this->data['tags_text'];
        }
        $tagsArr = [];
        $tagsList = $this->getTagsAttr($value, $data);
        foreach ($tagsList as $index => $item) {
            $tagsArr[] = $item->name;
        }
        return implode(',', $tagsArr);
    }

    public function getViewsFormatAttr($value, $data)
    {
        $result = $data['views'];
        if ($data['views'] > 1000) {
            $result = round($data['views'] / 1000, 1) . 'k';
        }
        return $result;
    }

    public function getUrlAttr($value, $data)
    {
        return addon_url('ask/question/show', [':id' => $data['id']], true);
    }

    public function getFullUrlAttr($value, $data)
    {
        return addon_url('ask/question/show', [':id' => $data['id']], true, true);
    }

    public function getStyleTextAttr($value, $data)
    {
        $color = $this->getAttr("style_color");
        $color = $color ? $color : "inherit";
        $color = str_replace(['#', ' '], '', $color);
        $bold = $this->getAttr("style_bold") ? "bold" : "normal";
        $underline = $this->getAttr("style_underline") ? "underline" : "none";
        $attr = [];
        if ($bold) {
            $attr[] = "font-weight:{$bold};";
        }
        if ($underline) {
            $attr[] = "text-decoration:{$underline};";
        }
        if (stripos($color, ',') !== false) {
            list($first, $second) = explode(',', $color);
            $attr[] = "background-image: -webkit-linear-gradient(0deg, #{$first} 0%, #{$second} 100%);background-image: linear-gradient(90deg, #{$first} 0%, #{$second} 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;";
        } else {
            $attr[] = "color:#{$color};";
        }

        return implode('', $attr);
    }

    public function getStyleColorFirstAttr($value, $data)
    {
        $color = $this->getAttr('style_color');
        $colorArr = explode(',', $color);
        return $colorArr[0];
    }

    public function getStyleColorSecondAttr($value, $data)
    {
        $color = $this->getAttr('style_color');
        $colorArr = explode(',', $color);
        return isset($colorArr[1]) ? $colorArr[1] : '';
    }

    public function getStyleBoldAttr($value, $data)
    {
        return in_array('b', explode('|', $data['style']));
    }

    public function getStyleUnderlineAttr($value, $data)
    {
        return in_array('u', explode('|', $data['style']));
    }

    public function getStyleColorAttr($value, $data)
    {
        $styleArr = explode('|', $data['style']);
        foreach ($styleArr as $index => $item) {
            if (preg_match('/\,|#/', $item)) {
                return $item;
            }
        }
        return '';
    }

    public function getFlagListAttr($value, $data)
    {
        return explode(',', $data['flag']);
    }

    /**
     * 悬赏是否已过期
     */
    public function getIsRewardExpiredAttr($value, $data)
    {
        return $data['price'] > 0 && (time() - $data['rewardtime']) > self::$config['adoptdays'] * 86400;
    }

    /**
     * 悬赏剩余时长
     */
    public function getRewardRemainSecondsAttr($value, $data)
    {
        $seconds = ($data['rewardtime'] ? $data['rewardtime'] : $data['createtime']) + self::$config['adoptdays'] * 86400 - time();
        $seconds = $seconds < 0 ? 0 : $seconds;
        return $seconds;
    }

    /**
     * 悬赏剩余时长(文字)
     */
    public function getRewardRemainTextAttr($value, $data)
    {
        $span = Date::span(($data['rewardtime'] ? $data['rewardtime'] : $data['createtime']) + self::$config['adoptdays'] * 86400, null, 'days,hours,minutes,seconds');
        $span['hours'] = str_pad($span['hours'], 2, "0", STR_PAD_LEFT);
        $span['minutes'] = str_pad($span['minutes'], 2, "0", STR_PAD_LEFT);
        $span['seconds'] = str_pad($span['seconds'], 2, "0", STR_PAD_LEFT);
        return "{$span['days']}天{$span['hours']}时{$span['minutes']}分{$span['seconds']}秒";
    }

    /**
     * 付费偷看是否已过期
     */
    public function getIsPeepExpiredAttr($value, $data)
    {
        if ($data['price'] > 0) {
            return $this->getIsRewardExpiredAttr($value, $data);
        } else {
            return (time() - $data['createtime']) > self::$config['adoptdays'] * 86400;
        }
    }

    /**
     * 付费偷看是否已禁用
     */
    public function getIsPeepDisabledAttr($value, $data)
    {
        $auth = Auth::instance();
        return $data['best_answer_id'] || $this->getIsPeepExpiredAttr($value, $data) || $auth->score < self::$config['limitscore']['peepsetting'];
    }

    /**
     * 获取付费偷看禁用原因
     */
    public function getPeepDisabledReasonAttr($value, $data)
    {
        $auth = Auth::instance();
        $reason = '';
        if ($data['best_answer_id']) {
            $reason = "提问者已经采纳最佳答案";
        } elseif ($this->getIsPeepExpiredAttr($value, $data)) {
            $reason = "提问者在 " . self::$config['adoptdays'] . " 天内未采纳任何最佳答案";
        } elseif ($auth->score < self::$config['limitscore']['peepsetting']) {
            $reason = "你的积分小于" . self::$config['limitscore']['peepsetting'];
        }
        return $reason;
    }

    /**
     * 获取问题列表
     */
    public static function getQuestionList($tag)
    {
        $category = !isset($tag['category']) ? '' : $tag['category'];
        $condition = empty($tag['condition']) ? '' : $tag['condition'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $flag = empty($tag['flag']) ? '' : $tag['flag'];
        $row = empty($tag['row']) ? 10 : (int)$tag['row'];
        $orderby = empty($tag['orderby']) ? 'createtime' : $tag['orderby'];
        $orderway = empty($tag['orderway']) ? 'desc' : strtolower($tag['orderway']);
        $limit = empty($tag['limit']) ? $row : $tag['limit'];
        $cache = !isset($tag['cache']) ? true : (is_bool($tag['cache']) ? $tag['cache'] : (int)$tag['cache']);
        $orderway = in_array($orderway, ['asc', 'desc']) ? $orderway : 'desc';
        $where = ['status' => ['<>', 'hidden']];

        $where['deletetime'] = ['exp', Db::raw('IS NULL')]; //by erastudio
        if ($category !== '') {
            $where['category_id'] = ['in', $category];
        }
        //如果有设置标志,则拆分标志信息并构造condition条件
        if ($flag !== '') {
            if (stripos($flag, '&') !== false) {
                $arr = [];
                foreach (explode('&', $flag) as $k => $v) {
                    $arr[] = "FIND_IN_SET('{$v}', flag)";
                }
                if ($arr) {
                    $condition .= "(" . implode(' AND ', $arr) . ")";
                }
            } else {
                $condition .= ($condition ? ' AND ' : '');
                $arr = [];
                foreach (array_merge(explode(',', $flag), explode('|', $flag)) as $k => $v) {
                    $arr[] = "FIND_IN_SET('{$v}', flag)";
                }
                if ($arr) {
                    $condition .= "(" . implode(' OR ', $arr) . ")";
                }
            }
        }
        $order = $orderby == 'rand' ? 'rand()' : (in_array($orderby, ['createtime', 'updatetime', 'answers', 'views', 'weigh', 'id']) ? "{$orderby} {$orderway}" : "createtime {$orderway}");
        $questionModel = self::with(['category', 'user']);

        $list = $questionModel
            ->where($where)
            ->where($condition)
            ->field($field)
            ->cache($cache)
            ->orderRaw($order)
            ->limit($limit)
            ->select();
        Tag::render($list, 'question');
        return $list;
    }

    /**
     * 获取上一页下一页
     * @param string $type
     * @param string $question
     * @param string $category
     * @return array
     */
    public static function getPrevNext($type, $question, $category)
    {
        $model = self::where('id', $type === 'prev' ? '<' : '>', $question)->where('status', 'normal');
        if ($category !== '') {
            $model->where('category_id', 'in', $category);
        }
        $model->order($type === 'prev' ? 'id desc' : 'id asc');
        $row = $model->find();
        return $row;
    }

    /**
     * 获取SQL查询结果
     */
    public static function getQueryList($tag)
    {
        $sql = isset($tag['sql']) ? $tag['sql'] : '';
        $bind = isset($tag['bind']) ? $tag['bind'] : [];
        $cache = !isset($tag['cache']) ? true : (int)$tag['cache'];
        $name = md5("sql-" . $tag['sql']);
        $list = Cache::get($name);
        if (!$list) {
            $list = \think\Db::query($sql, $bind);
            Cache::set($name, $list, $cache);
        }
        return $list;
    }

    public static function getIndexQuestionList($type, $category_id = null, $user_id = null, $tag_id = null, $keyword = null)
    {
        $typeArr = [
            'new'       => 'createtime',
            'hot'       => 'answers',
            'price'     => 'createtime',
            'unsolved'  => 'createtime',
            'unanswer'  => 'createtime',
            'unsettled' => 'rewardtime'
        ];
        $questionModel = self::with(['category', 'user']);

        $list = $questionModel
            ->where('status', '<>', 'hidden')
            ->where(function ($query) use ($type, $category_id, $user_id, $tag_id) {
                if ($type == 'price') {
                    $query->where('price', '>', 0);
                } elseif ($type == 'unsolved') {
                    $query->where('best_answer_id', '=', 0);
                    $query->where('status', '=', 'normal');
                } elseif ($type == 'unanswer') {
                    $query->where('answers', '=', 0);
                } elseif ($type == 'unsettled') {
                    $query->where('price', '>', 0);
                    $query->where('best_answer_id', '=', 0);
                    $query->where('rewardtime', '<', strtotime("-" . self::$config['adoptdays'] . ' days'));
                }
                if ($category_id) {
                    $query->where('category_id', '=', $category_id);
                }
                if ($user_id) {
                    $query->where('user_id', '=', $user_id);
                }
                if ($tag_id) {
                    $query->where('id', 'in', function ($query) use ($tag_id) {
                        $query->name("ask_taggable")->where("type", "question")->where("tag_id", 'in', $tag_id)->field("source_id");
                    });
                }
            })
            ->where(function ($query) use ($keyword) {
                $arr = array_filter(explode(' ', $keyword));
                foreach ($arr as $index => $item) {
                    $query->where('title', 'like', "%{$item}%");
                }
            })
            ->order(isset($typeArr[$type]) ? $typeArr[$type] : $typeArr['new'], $type == 'unsettled' ? 'asc' : 'desc')
            ->paginate(self::$config['pagesize']['question'], true);
        //渲染标签
        Tag::render($list, 'question');
        return $list;
    }

    public static function getTagQuestionList($tag, $type, $page = 1)
    {
        $pagesize = 10;
        $typeArr = [
            'new'      => 'createtime',
            'hot'      => 'answers',
            'price'    => 'createtime',
            'unanswer' => 'createtime'
        ];
        $tag = [
            'cache'    => false,
            'limit'    => ($page - 1) * $pagesize . ',10',
            'orderby'  => isset($typeArr[$type]) ? $typeArr[$type] : $typeArr['new'],
            'orderway' => 'desc'
        ];
        $tag['condition'] = function ($query) use ($tag, $type) {
            if ($type == 'price') {
                $query->where('price', '>', 0);
            } elseif ($type == 'unanswer') {
                $query->where('answers', '=', 0);
            }
            $query->where('id', 'in', function ($query) use ($tag) {
                $query->name("ask_taggable")->where("type", "question")->where("tag_id", $tag->id)->field("source_id");
            });
        };
        return self::getQuestionList($tag);
    }

    public function refund()
    {
        Db::startTrans();
        try {
            $price = $this->price;
            $this->price = 0;
            $this->rewardtime = null;
            $this->save();
            \app\common\model\User::money($price, $this->user_id, "悬赏问题退款");
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
        }
    }

    /**
     * 关联分类模型
     */
    public function category()
    {
        return $this->belongsTo('Category', 'category_id', 'id')->setEagerlyType(1);
    }

    /**
     * 关联会员模型
     */
    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id')->setEagerlyType(1);
    }
}
