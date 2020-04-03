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
     * 设置微信支付配置文件
     * @param integer $data
     * @return array
     */
    protected function setConfig($data)
    {
        $this->payInfo = $this->payModel->where(['team_id'=>$data])->find();
        if (empty($this->payInfo)) {
            dump(empty($this->payInfo));
            die("支付信息有误");
        }
        return $this->weChatConfig = [
//            'token'          => 'RPyzZEPt5RiAYWxr4Dks87bpQWixRadf',
//            'appid'          => 'wx90588380da4a2bb0',
//            'appsecret'      => '5e1df5e5002bfc5e190a74e0b438e7a6',
//            'encodingaeskey' => 'NT5JWjUDqgDhkgKb6xvmSTpIdtSr2b4oYlaYkeJ5YPO',
//            // 配置商户支付参数（可选，在使用支付功能时需要）
//            'mch_id'         => "1583492131",
//            'mch_key'        => '7e8763b61b23b4c42526e1055c2bbfb1',
//            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
//            'ssl_key'        => '',
//            'ssl_cer'        => '',
//            // 缓存目录配置（可选，需拥有读写权限）
//            'cache_path'     => '',

            'token'          => $this->payInfo['token'],
            'appid'          => $this->payInfo['app_id'],
            'appsecret'      => $this->payInfo['app_secret'],
            'encodingaeskey' => $this->payInfo['encodingaeskey'],
            // 配置商户支付参数（可选，在使用支付功能时需要）
            'mch_id'         => $this->payInfo['mch_id'],
            'mch_key'        => $this->payInfo['mch_key'],
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => '',
            'ssl_cer'        => '',
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => APP_PATH.'/runtime/pay/',
        ];
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
                dump($oauth);die;
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