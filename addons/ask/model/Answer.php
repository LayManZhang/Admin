<?php

namespace addons\ask\model;

use addons\ask\library\Askdown;
use addons\ask\library\Service;
use app\common\library\Auth;
use think\Db;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;

/**
 * 回答模型
 */
class Answer Extends Model
{

    protected $name = "ask_answer";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected $auto = ['content_fmt'];
    // 追加属性
    protected $append = [
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
            Askdown::instance()->notification('answer', $row->id);
            User::increase('answers', 1, $row->user_id);
            $config = get_addon_config('ask');
            //增加积分
            $config['score']['postanswer'] && \app\common\model\User::score($config['score']['postanswer'], $row->user_id, '发布答案');
        });
        self::afterDelete(function ($row) use ($config) {
            $commentList = Comment::where('type', 'answer')->where('source_id', $row['id'])->select();
            foreach ($commentList as $index => $item) {
                $item->delete();
            }
            $model = Service::getModelByType('question', $row['question_id']);
            if ($model) {
                try {
                    $model->setDec("answers");
                } catch (Exception $e) {

                }
            }
            User::decrease('answers', 1, $row->user_id);
            //减少积分
            $config['score']['postanswer'] && \app\common\model\User::score(-$config['score']['postanswer'], $row->user_id, '删除答案');
        });
        self::afterUpdate(function ($row) {
            $changedData = $row->getChangedData();
            if (isset($changedData['adopttime'])) {
                if ($changedData['adopttime']) {
                    User::increase('adoptions', 1, $row->user_id);
                } else {
                    User::decrease('adoptions', 1, $row->user_id);
                }
            }
        });
    }

    public function getCreateDateAttr($value, $data)
    {
        return time() - $data['createtime'] > 7 * 86400 ? date("Y-m-d", $data['createtime']) : human_date($data['createtime']);
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

    public function getPeepDaysAttr($value, $data)
    {
        $days = abs(self::$config['adoptdays'] - intval((time() - $data['createtime']) / 86400));
        $days = $days <= 0 ? 1 : $days;
        return $days;
    }

    //获取付费偷看状态
    public function getPeepStatus($question)
    {
        if (isset($this->data['peep_status'])) {
            return $this->data['peep_status'];
        }
        $user_id = Auth::instance()->id;

        //如果金额为0、提问者、回答者本人、管理员 均可查看
        if ($question->user_id == $user_id || $this->data['price'] == 0 || $this->data['user_id'] == $user_id || Service::isAdmin()) {
            return 'noneed';
        } else {
            if ($question->best_answer_id) {
                if ($this->data['id'] == $question->best_answer_id) {
                    //判断是否在有效期内采纳
                    if ($this->data['adopttime'] - ($question['rewardtime'] ? $question['rewardtime'] : $question['createtime']) < self::$config['adoptdays'] * 86400) {
                        $paid = \addons\ask\library\Order::check('answer', $this->data['id']);
                        return $paid ? 'paid' : 'unpaid';
                    } else {
                        //如果在有效期外采纳，则不再需要付费
                        return 'noneed';
                    }
                } else {
                    //未采纳
                    return 'unadopted';
                }
            } else {
                if (time() - $this->data['createtime'] > self::$config['adoptdays'] * 86400) {
                    return 'expired';
                } else {
                    return 'waiting';
                }
            }
        }
    }

    /**
     * 获取回答列表
     * @param int $question_id
     * @param null $user_id
     * @param string $order
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function getAnswerList($question_id, $user_id = null, $order = 'default')
    {
        $question = Question::get($question_id);
        if (!$question) {
            return [];
        }
        $answerList = self::with('user')
            ->where('question_id', $question['id'])
            ->where(function ($query) use ($user_id) {
                if (!is_null($user_id)) {
                    $query->where('user_id', $user_id);
                }
            })
            ->where('status', 'normal')
            ->whereNotIn('id', $question['best_answer_id'])
            ->field(Db::raw("*,(1.*`voteup`-`votedown`) AS vote"))
            ->order($order == 'default' ? 'vote DESC, id ASC' : 'createtime DESC')
            ->paginate(self::$config['pagesize']['answer']);

        Collection::render($answerList, 'answer');
        Vote::render($answerList, 'answer');
        return $answerList;
    }

    public static function notify($order)
    {
        $answer = self::get($order->source_id);
        if ($answer) {
            $answer->setInc("sales");
            $config = get_addon_config('ask');
            if ($config['peepanswerratio']) {
                list($systemRatio, $quizzerRatio, $answerRatio) = explode(':', $config['peepanswerratio']);
                //付费偷看答案分成
                $systemRatio > 0 && \app\common\model\User::money($systemRatio * $answer->price, $config['system_user_id'], '付费偷看答案分成');
                $quizzerRatio > 0 && \app\common\model\User::money($quizzerRatio * $answer->price, $answer->question->user_id, '付费偷看答案分成');
                $answerRatio > 0 && \app\common\model\User::money($answerRatio * $answer->price, $answer->user_id, '付费偷看答案分成');
            }
        }
    }

    /**
     * 关联会员模型
     */
    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id')->setEagerlyType(1);
    }

    /**
     * 关联文章模型
     */
    public function question()
    {
        return $this->belongsTo('\addons\ask\model\Question', 'question_id', 'id')->setEagerlyType(1);
    }

}
