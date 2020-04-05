<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use think\Session;
use WeChat\Pay;

/**
 * 支付类
 * Class PayOrder
 * @package app\index\controller
 */
class PayOrder extends Frontend
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取客户端IP
     */
    public function getClientIp()
    {
        $cip = 'unknown';
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip  = getenv("REMOTE_ADDR");
        }

        return $cip;
    }

    /**
     * 微信支付
     */
    public function WeChatPay()
    {
        //接收订单ID与团队ID和业务员id参数
        $params = $this->request->param();

        if (Cache::has($params['sn'])) {
            //表示订单真实有效，可以进行支付
            $orderInfo = Cache::get($params['sn']);
            $payInfo = $this->getPayInfo($orderInfo['team_id']);
            $weChatConfig = $this->setConfig($payInfo);
            // 创建接口实例
            $weChat = new Pay($weChatConfig);
            // 组装参数，可以参考官方商户文档
            $options = [
                'body'             => $orderInfo['production_name'],/*商品名称*/
                'out_trade_no'     => $params['sn'],/*自己系统的订单号*/
                'total_fee'        => true == Env::get('app.debug') ? 1 : $orderInfo['price']*100,/*价格，单位：分*/
                'openid'           =>  Cache::get($params['sn'])['openid'],/*微信网页授权openid*/
                'trade_type'       => 'JSAPI',/*支付类型，JSAPI--JSAPI支付（或小程序支付）*/
                'notify_url'       => 'http://notify.ckjdsak.cn/index.php/index/notify/WeChatNotify',/*回调地址*/
                'spbill_create_ip' => $this->getClientIp(),
            ];
            // 尝试创建订单
            $wxOrder = $weChat->createOrder($options);
            $result = $weChat->createParamsForJsApi($wxOrder['prepay_id']);
            // 订单数据处理
            $this->assign('jsApiPrepay',json_encode($result));
            $this->assign('orderInfo',$orderInfo);
            return $this->view->fetch('wechatpay');
        } else {
            //表示非法请求
            die('你请求的支付地址有错误，请重新下单支付');
        }

    }


}