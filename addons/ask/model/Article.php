<?php

namespace addons\ask\model;

use addons\ask\library\Askdown;
use app\common\library\Auth;
use think\Db;
use think\Model;
use traits\model\SoftDelete;

/**
 * 文章模型
 */
class Article Extends Model
{

    protected $name = "ask_article";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected $auto = ['content_fmt'];

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
            Askdown::instance()->notification('article', $row->id);
            User::increase('articles', 1, $row->user_id);
            //增加积分
            $config['score']['postarticle'] && \app\common\model\User::score($config['score']['postarticle'], $row->user_id, '发布文章');
        });
        self::afterDelete(function ($row) use ($config) {
            //删除相关评论
            $commentList = Comment::where('type', 'article')->where('source_id', $row['id'])->select();
            foreach ($commentList as $index => $item) {
                $item->delete();
            }
            User::decrease('articles', 1, $row->user_id);
            //减少积分
            $config['score']['postarticle'] && \app\common\model\User::score(-$config['score']['postarticle'], $row->user_id, '删除文章');
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

    public function getCreateDateAttr($value, $data)
    {
        return time() - $data['createtime'] > 7 * 86400 ? date("Y-m-d", $data['createtime']) : human_date($data['createtime']);
    }


    public function getPaidStatusAttr($value, $data)
    {
        if (isset($this->data['paid_status'])) {
            return $this->data['paid_status'];
        }
        $user_id = Auth::instance()->id;

        //如果金额为0、本人、管理员 均可查看
        if ($data['user_id'] == $user_id || $data['price'] == 0) {
            return 'noneed';
        } else {
            if ($this->getAttr('paid')) {
                return 'paid';
            } else {
                return 'unpaid';
            }
        }
    }

    public function getIsPaidPartOfContentAttr($value, $data)
    {
        $pattern = '/\$\$paidbegin\$\$(.*?)\$\$paidend\$\$/is';
        return preg_match($pattern, $value);
    }

    public function getContentOutputAttr($value, $data)
    {
        return str_replace(["<", ">"], ["&lt;", "&gt;"], $data['content']);
    }

    public function getContentFmtPartAttr($value, $data)
    {
        //如果内容中包含有付费标签
        $pattern = '/\$\$paidbegin\$\$(.*?)\$\$paidend\$\$/is';
        if (preg_match($pattern, $value) && !$this->getAttr('paid')) {
            $money = (int)Auth::instance()->money;
            $btn = "<a href='javascript:' class='btn btn-primary btn-paynow' style='color:white' data-id='{$data['id']}' data-type='article' data-price='{$data['price']}' data-money='{$money}'>内容已经隐藏，点击付费后查看</a>";
            $value = preg_replace($pattern, "<div class='alert alert-warning alert-paid'>{$btn}</div>", $value);
        }
        return $value;
    }

    public function getContentFmtAttr($value, $data)
    {
        //如果内容中包含有付费标签
        $pattern = '/\$\$paidbegin\$\$(.*?)\$\$paidend\$\$/is';
        if (preg_match($pattern, $value)) {
            $value = preg_replace($pattern, "$1", $value);
        }
        return $value;
    }

    public function setContentFmtAttr($data)
    {
        $content = Askdown::instance()->format($this->data['content']);
        return $content;
    }

    public function setTagData($data)
    {
        $this->data['tags'][] = $data;
    }

    public function getUrlAttr($value, $data)
    {
        return addon_url('ask/article/show', [':id' => $data['id']], true);
    }

    public function getFullUrlAttr($value, $data)
    {
        return addon_url('ask/article/show', [':id' => $data['id']], true, true);
    }

    public function getPaidAttr($value, $data)
    {
        if (isset($this->data['paid'])) {
            return $this->data['paid'];
        }
        $this->data['paid'] = \addons\ask\library\Order::check('article', $data['id']);
        return $this->data['paid'];
    }

    public function getTagsAttr($value, $data)
    {
        if (isset($this->data['tags'])) {
            return $this->data['tags'];
        }
        $tags = Tag::getTags('article', $data['id']);
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

    public function getIscustompriceAttr($value, $data)
    {
        if ($data['price'] == 0) {
            return false;
        } else if (in_array($data['price'], self::$config['pricelist'])) {
            return false;
        }
        return true;
    }

    public static function getIndexArticleList($type, $category_id, $user_id = null, $tag_id = null, $keyword = null)
    {
        $typeArr = [
            'new'   => 'createtime',
            'hot'   => 'views',
            'price' => 'createtime',
        ];
        $articleModel = self::with(['category', 'user']);

        $list = $articleModel
            ->where('status', '<>', 'hidden')
            ->where(function ($query) use ($type, $category_id, $user_id, $tag_id) {
                if ($type == 'price') {
                    $query->where('price', '>', 0);
                }
                if ($user_id) {
                    $query->where('user_id', '=', $user_id);
                }
                if ($category_id) {
                    $query->where('category_id', '=', $category_id);
                }
                if ($tag_id) {
                    $query->where('id', 'in', function ($query) use ($tag_id) {
                        $query->name("ask_taggable")->where("type", "article")->where("tag_id", 'in', $tag_id)->field("source_id");
                    });
                }
            })
            ->where(function ($query) use ($keyword) {
                $arr = array_filter(explode(' ', $keyword));
                foreach ($arr as $index => $item) {
                    $query->where('title', 'like', "%{$item}%");
                }
            })
            ->order(isset($typeArr[$type]) ? $typeArr[$type] : $typeArr['new'], 'desc')
            ->paginate(self::$config['pagesize']['article'], true);
        //渲染标签
        Tag::render($list, 'article');
        //渲染投票
        Vote::render($list, 'article');
        return $list;
    }

    public static function getArticleList($tag)
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
                if ($arr)
                    $condition .= "(" . implode(' AND ', $arr) . ")";
            } else {
                $condition .= ($condition ? ' AND ' : '');
                $arr = [];
                foreach (array_merge(explode(',', $flag), explode('|', $flag)) as $k => $v) {
                    $arr[] = "FIND_IN_SET('{$v}', flag)";
                }
                if ($arr)
                    $condition .= "(" . implode(' OR ', $arr) . ")";
            }
        }
        $order = $orderby == 'rand' ? 'rand()' : (in_array($orderby, ['createtime', 'updatetime', 'views', 'weigh', 'id']) ? "{$orderby} {$orderway}" : "createtime {$orderway}");

        $articleModel = self::with(['category', 'user']);

        $list = $articleModel
            ->where($where)
            ->where($condition)
            ->field($field)
            ->cache($cache)
            ->orderRaw($order)
            ->limit($limit)
            ->select();
        Tag::render($list, 'article');
        return $list;
    }

    public static function notify($order)
    {
        $article = self::get($order->source_id);
        if ($article) {
            $config = get_addon_config('ask');
            if ($config['peepanswerratio']) {
                list($systemRatio, $authorRatio) = explode(':', $config['articleratio']);
                //付费文章分成
                $systemRatio > 0 && \app\common\model\User::money($systemRatio * $article->price, $config['system_user_id'], '付费文章分成');
                $authorRatio > 0 && \app\common\model\User::money($authorRatio * $article->price, $article->user_id, '付费文章分成');
            }
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
