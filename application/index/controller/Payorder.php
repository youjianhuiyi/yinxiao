<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\sysconfig\Pay as PayModel;
use think\Env;
use think\Session;
use WeChat\Oauth;
use WeChat\Pay;

/**
 * 支付类
 * Class PayOrder
 * @package app\index\controller
 */
class PayOrder extends Frontend
{
    protected $orderModel = null;
    protected $payModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->payModel = new PayModel();
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
            $this->payInfo = $this->getPayInfo($orderInfo['team_id']);
            $this->weChatConfig = $this->setConfig($this->payInfo);
            // 创建接口实例
            $weChat = new Pay($this->weChatConfig);
            // 组装参数，可以参考官方商户文档
            $options = [
                'body'             => $orderInfo['production_name'],
                'out_trade_no'     => time(),
                'total_fee'        => false === Env::get('app.debug') ? 1 : $orderInfo['price'],
                'openid'           => isset($params['openid']) ?: Session::get('openid'),
                'trade_type'       => 'JSAPI',
                'notify_url'       => 'http://notify.ckjdsak.cn/index.php/index/notify/WeChatNotify',
                'spbill_create_ip' => $this->request->ip(),
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