<?php

namespace addons\ask\library;

use addons\ask\model\Answer;
use addons\ask\model\Article;
use addons\ask\model\Thanks;
use app\common\library\Auth;
use app\common\model\User;
use fast\Http;
use fast\Random;
use think\Db;
use think\Exception;
use think\Hook;
use think\Request;
use think\View;

class Order
{


    /**
     * 获取查询条件
     * @return \Closure
     */
    protected static function getQueryCondition()
    {
        $condition = function ($query) {

            $auth = Auth::instance();
            $user_id = $auth->isLogin() ? $auth->id : 0;
            $ip = Request::instance()->ip();

            if ($user_id) {
                $query->whereOr('user_id', $user_id)->whereOr('ip', $ip);
            } else {
                $query->where('user_id', 0)->where('ip', $ip);
            }

        };
        return $condition;
    }

    /**
     * 检查订单
     * @param string $type
     * @param mixed  $source_id
     * @return bool
     * @throws Exception
     */
    public static function check($type, $source_id)
    {
        $model = Service::getModelByType($type, $source_id);
        if (!$model) {
            return false;
        }
        $where = [
            'type'      => $type,
            'source_id' => $source_id,
            'status'    => 'paid',
        ];

        //匹配已支付订单
        $order = \addons\ask\model\Order::where($where)->where(self::getQueryCondition())->order('id', 'desc')->find();
        return $order ? true : false;
    }

    /**
     * 发起订单支付
     * @param string $type
     * @param int    $source_id
     * @param float  $money
     * @param string $paytype
     * @param string $title
     * @throws OrderException
     */
    public static function submit($type, $source_id, $money, $paytype = 'wechat', $title = '')
    {
        $order = \addons\ask\model\Order::where('type', $type)
            ->where('source_id', $source_id)
            ->where(self::getQueryCondition())
            ->order('id', 'desc')
            ->find();
        if ($order && $order['status'] == 'paid') {
            throw new OrderException('订单已支付');
        }
        $auth = Auth::instance();
        $request = Request::instance();
        $user_id = $auth->id ? $auth->id : 0;
        $title = $title ? $title : '支付';
        $orderid = date("YmdHis") . sprintf("%06d", $user_id) . mt_rand(1000, 9999);
        if (!$order) {
            $data = [
                'user_id'   => $user_id,
                'type'      => $type,
                'orderid'   => $orderid,
                'source_id' => $source_id,
                'title'     => $title,
                'amount'    => $money,
                'payamount' => 0,
                'paytype'   => $paytype,
                'ip'        => $request->ip(),
                'useragent' => $request->server('HTTP_USER_AGENT'),
                'status'    => 'created'
            ];
            $order = \addons\ask\model\Order::create($data);
        } else {
            if ($order->amount != $money) {
                $order->amount = $money;
                $order->save();
            }
        }
        //使用余额支付
        if ($paytype == 'balance') {
            if (!$auth->id) {
                throw new OrderException('需要登录后才能够支付');
            }
            if ($auth->money < $money) {
                throw new OrderException('余额不足，无法进行支付');
            }
            Db::startTrans();
            try {
                User::money(-$money, $auth->id, $title);
                self::settle($order->orderid);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                throw new OrderException($e->getMessage());
            }
            throw new OrderException('余额支付成功', 1);
        }

        $epay = get_addon_info('epay');

        if ($epay && $epay['state']) {
            $notifyurl = addon_url('ask/order/epay', [], false, true) . '/type/notify/paytype/' . $paytype;
            $returnurl = addon_url('ask/order/epay', [], false, true) . '/type/return/paytype/' . $paytype;

            \addons\epay\library\Service::submitOrder($order['amount'], $order['orderid'], $paytype, $title, $notifyurl, $returnurl);
        } else {
            $result = Hook::listen('ask_order_submit', $order);
            if (!$result) {
                throw new OrderException("请先在后台安装配置微信支付宝整合插件");
            }
        }
    }

    /**
     * 订单结算
     * @param mixed  $orderid
     * @param mixed  $payamount
     * @param string $memo
     * @return bool
     */
    public static function settle($orderid, $payamount = null, $memo = '')
    {
        $order = \addons\ask\model\Order::getByOrderid($orderid);
        if (!$order) {
            return false;
        }
        if ($order['status'] != 'paid') {
            $order->payamount = $payamount ? $payamount : $order->amount;
            $order->paytime = time();
            $order->status = 'paid';
            $order->memo = $memo;
            $order->save();
            if ($order['type'] == 'thanks') {
                Thanks::notify($order);
            } else if ($order['type'] == 'answer') {
                Answer::notify($order);
            } else if ($order['type'] == 'article') {
                Article::notify($order);
            }
        }
        return true;
    }

}