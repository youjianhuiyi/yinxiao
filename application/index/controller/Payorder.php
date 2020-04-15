<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use think\Session;
use WeChat\Oauth;
use app\admin\model\order\Order as OrderModel;
use WeChat\Pay;
use app\admin\model\sysconfig\Xpay as XpayModel;

/**
 * 支付类
 * Class PayOrder
 * @package app\index\controller
 */
class PayOrder extends Frontend
{
    protected $orderModel = null;
    protected $xpayModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->xpayModel = new XpayModel();
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
     * 微信授权
     */
    public function WeChatGrant()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        //访问鉴权，如果链接不正确，则直接终止访问
        if (isset($params['code']) && !empty($params['code'])) {
            $paramsNew = $this->request->param();
            if (!$this->verifyCheckKey($paramsNew)) {
                //表示验证失败，链接被篡改
                die("请不要使用非法手段更改链接");
            }
            //判断code是否已经缓存 ，因为每个code只能使用一次，并且有效时间为5分钟
            if (Cache::has($paramsNew['code'])) {
                $wxUserInfo = Cache::get($paramsNew['code']);
            } else {
                $payInfo = $this->getPayInfo($paramsNew['tid']);
                $weChatConfig = $this->setConfig($payInfo);
                // 实例接口
                $weChat = new Oauth($weChatConfig);
                // 执行操作
                $wxUserInfo = $weChat->getOauthAccessToken();
                //pay_domain_1缓存，记录支付域名，和支付信息一起，记录当前访问用户与固定一个支付域名绑定，30分钟。
                Cache::set($paramsNew['code'],$wxUserInfo,Env::get('redis.expire'));
                Session::set('openid',$wxUserInfo['openid']);
            }

            //准备开始配置支付参数与调用支付
            //表示已经获取了openid
            if (Cache::has($paramsNew['sn'])) {
                //表示订单真实有效，可以进行支付
                $orderInfo = Cache::get($params['sn']);
                $payInfo = $this->getPayInfo($orderInfo['team_id']);
                $weChatConfig = $this->setConfig($payInfo);
                // 创建接口实例
                $weChat = new Pay($weChatConfig);
                // 组装参数，可以参考官方商户文档
                $options = [
                    'body'              => $orderInfo['production_name'],/*商品名称*/
                    'out_trade_no'      => $params['sn'],/*自己系统的订单号*/
                    'total_fee'         => true == Env::get('app.debug') ? 1 : $orderInfo['price'] * 100,/*价格，单位：分*/
                    'openid'            => $wxUserInfo['openid'],/*微信网页授权openid*/
                    'trade_type'        => 'JSAPI',/*支付类型，JSAPI--JSAPI支付（或小程序支付）*/
                    'notify_url'        => 'http://notify.ckjdsak.cn/index.php/index/notify/WeChatNotify',/*回调地址,需要指定具体的值*/
                    'spbill_create_ip'  => $this->getClientIp(),
                ];
                Cache::set('payorder',$options);
                //更新订单Openid
//            $this->orderModel->where(['id'=>$params['oid']])->isUpdata(true)->save(['openid'=>$wxUserInfo['openid'],'id'=>$orderInfo['oid']]);
                // 尝试创建订单
                $wxOrder = $weChat->createOrder($options);
                $result = $weChat->createParamsForJsApi($wxOrder['prepay_id']);
                $returnData = [
                    'jsapi' => $result,
                    'order_info' => $orderInfo
                ];
                Cache::set($wxUserInfo['openid'],$returnData);
                //跳转到微信支付
                header('Location:'.'http://pay.ckjdsak.cn/index.php/index/payorder/readypay?openid='.$wxUserInfo['openid']);
                // 订单数据处理
//                $this->assign('jsApiPrepay', json_encode($result));
//                $this->assign('orderInfo', $orderInfo);
//                return $this->view->fetch('wechatpay');
            } else {
                //表示非法请求
                die('你请求的支付地址有错误，请重新下单支付');
            }

        } else {
            $this->intoBefore($params);
        }

    }

    /**
     * 微信支付
     */
    public function readyPay()
    {
        $param = $this->request->param();
        if (Cache::has($param['openid'])) {
            //表示是正常的订单支付
            $data = Cache::get($param['openid']);
            $this->assign('jsApiPrepay', json_encode($data['jsapi']));
            $this->assign('orderInfo', $data['order_info']);
            return $this->view->fetch('wechatpay');
        } else {
            //表示非法请求
            die('你请求的支付地址有错误，请重新下单支付');
        }

    }

    /**
     * 享钱支付
     */
    public function xpay()
    {

    }
}