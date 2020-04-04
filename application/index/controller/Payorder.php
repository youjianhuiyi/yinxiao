<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\sysconfig\Pay as PayModel;
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
        $params = $this->request->get();
        if (Cache::has($params['sn'])) {
            //表示订单真实有效，可以进行支付
            $orderInfo = Cache::get($params['sn']);
//            $payInfo = $this->payModel->where(['status'=>1,'team_id'=>$orderInfo['team_id']])->get();
            $this->setConfig($orderInfo['team_id']);

            try {
                // 实例接口
                $oauth = new Oauth($this->weChatConfig);
                dump($this->weChatConfig);
                dump($oauth);
                // 执行操作
                $userInfo = $oauth->getOauthAccessToken();
                dump($userInfo);die;
                // 创建接口实例
                $weChat = new Pay($this->weChatConfig);

                // 组装参数，可以参考官方商户文档
                $options = [
                    'body'             => '测试商品',
                    'out_trade_no'     => time(),
                    'total_fee'        => '1',
                    'openid'           => $userInfo['openid'],
                    'trade_type'       => 'JSAPI',
                    'notify_url'       => 'http://notify.ckjdsak.cn/index.php/index/notify/WeChatNotify',
                    'spbill_create_ip' => $this->request->ip(),
                ];

                // 尝试创建订单
                $result = $weChat->createOrder($options);

                // 订单数据处理
                var_export($result);

            } catch(Exception $e) {

                // 出错啦，处理下吧
                echo $e->getMessage() . PHP_EOL;

            }
        }

    }


}