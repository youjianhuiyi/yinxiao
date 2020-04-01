<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use Yansongda\Pay\Pay;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\sysconfig\Pay as PayModel;

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
     * 设置微信支付配置文件
     * @param $data
     * @return array
     */
    protected function setConfig($data)
    {
        return $this->weChatConfig = [
//            'appid'     => 'wxb3fxxxxxxxxxxx', // APP APPID
            'app_id'    => $data['app_id'], // 公众号 APPID
//            'miniapp_id'=> 'wxb3fxxxxxxxxxxx', // 小程序 APPID
            'mch_id'    => $data['business_code'],
            'key'       => $data['pay_secret'],
            'notify_url' => 'http://yanda.net.cn/notify.php',
            'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
            'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
            'log' => [ // optional
                'file' => './logs/wechat.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
            'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
        ];
    }
    /**
     * 微信支付
     */
    public function WeChatPay()
    {
        //接收订单ID与团队ID和业务员id参数
        $params = $this->request->get();
        //通过接收到的参数获取订单信息
        $orderInfo = $this->orderModel->get($params['order_id']);
        if (Cache::has($orderInfo['sn'].$params['order_id'])) {
            //表示订单真实有效，可以进行支付
            $payInfo = $this->payModel->where(['status'=>1,'team_id'=>$orderInfo['team_id']])->get();

            $this->setConfig($payInfo);

            $order = [
                'out_trade_no' => time(),
                'total_fee' => '1', // **单位：分**
                'body' => 'test body - 测试',
                'openid' => 'onkVf1FjWS5SBIixxxxxxx',
            ];


            $pay = Pay::wechat($this->weChatConfig)->wap($order);

            // $pay->appId
            // $pay->timeStamp
            // $pay->nonceStr
            // $pay->package
            // $pay->signType
        }
            
    }


}