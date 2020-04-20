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
     * 如意付支付
     * @return string
     * @throws \think\Exception
     */
    public function rypayOrder()
    {
//        $params = $this->request->param();
//        $orderInfo = Cache::get($params['sn']);
//
//        if ($this->request->isPost()) {
            $params = $this->request->param();
            $orderInfo = Cache::get($params['sn']);
            $payInfo = Cache::get($orderInfo['order_ip'].'-rypay_config');
            $url = time().'.'.Cache::get('luck_domain');
            $data = [
                'mchId'         =>  $payInfo['mch_id'],/*分配的商户号*/
                'appId'         =>  $payInfo['app_id'],/*该商户创建的应用对应的ID*/
                'productId'     =>  $payInfo['product_id'],/*支付产品ID*/
                'mchOrderNo'    =>  $params['sn'],/*商户生成的订单号*/
                'currency'      =>  'cny',/*三位货币代码,人民币:cny*/
                'amount'        =>  Env::get('app.debug') ? 500 : $orderInfo['price'] * 100,/*支付金额,单位分*/
                'returnUrl'     =>  'http://'.$url.'/index.php/index/order/orderquery',/*支付结果回调URL*/
                'notifyUrl'     =>  'http://'.$url.'/index.php/index/notify/rypayNotify',/*支付结果回调URL*/
                'subject'       =>  $orderInfo['production_name'],/*商品主题*/
                'body'          =>  $orderInfo['goods_info'],/*商品描述信息*/
                'extra'         =>  '',/*特定渠道发起时额外参数,见下面说明*/
                'sign'          =>  '',/*签名值，详见签名算法*/
            ];
            //生成签名
            $newParams = $this->RyPaySignParams($data,$payInfo['mch_key']);
            $data['sign'] = $newParams;
            //发起请求之前判断当前是不是已经请求过一次了
            if (!Cache::has('ry-'.$params['sn'])) {
                //发起POST请求，获取订单信息
                $result = $this->curlPostForm($data, $payInfo['api_url']);
                Cache::set('ry-'.$params['sn'],$result,600);
            } else {
                $result = Cache::get('ry-'.$params['sn']);
            }
            /*****接收返回数据进行验签与返回处理***/
            //构建页面展示需要的数据
            $newData = json_decode($result,true);
            //处理返回值验签
//            $newSign = $this->RyPaySignParams($newData,$payInfo['mch_key']);
            //表示验签成功
//            $newResult = json_encode($newData);
//            echo $newResult;
//            die;

            if ($newData['retCode'] == 'SUCCESS') {
                header('Location:'.$newData['payParams']['payUrl']);
            } else {
                echo "<script>alert('支付异常，请重新下单'.{$newData['retMsg']})</script>";
                die;
            }

            die;
//        }
//
//        $this->assign('orderInfo',$orderInfo);
//        return $this->view->fetch('rypay');
    }

    /**
     * XPAY订单支付
     * @comment 其他不需要授权的支付。
     */
    public function orderPayment()
    {
        $params = $this->request->param();
        $orderInfo = $this->orderModel->where('sn',$params['sn'])->find();
        $payInfo = Cache::get($orderInfo['order_ip'].'-xpay_config');
        //由于下单逻辑和支付逻辑有冲突，这里需要生一个临时订单号，用于支付使用。与当前订单不一样，但需要建议绑定关系。
        if (!Cache::has('x-'.$params['sn'])) {
            $url = time().'.'.Cache::get('luck_domain');
            $data = [
                'ticket'    => time(),/*用来匹配请求*/
                'service'   => 'pay.xiangqian.wxjspay',
                'version'   => '2.0',/*版本号 默认是2.0*/
                'sign_type' => 'MD5',/*签名方式，默认是md5*/
                'mch_code'  => $payInfo['mch_code'],/*商户号 享多多系统的门店编码*/
                'timestamp' => date('YmdHis',time()),/*时间戳 发送请求的时间，格式"yyyyMMddHHmmss"*/
                'sign'      => '',/*签名*/
                'body'      => [
                    'orderNo'       => $params['sn'],/*商户订单号 商户系统内部的订单号 ,32个字符内、 可包含字母,确保在商户系统唯一*/
                    'order_info'    => $orderInfo['production_name'],/*商品描述*/
                    'total_amount'  => Env::get('app.debug') ? 1 : $orderInfo['price'] * 100,/*总金额，以分为单位，不允许包含任何字、符号*/
                    'mch_create_ip' => $this->request->ip(),/*订单生成的机器 IP*/
                    'notify_url'    => 'http://'.$url.'/index.php/index/notify/xpayNotify',
                    'sub_appid' => $payInfo['app_id'],/*wx092575bf6bc1636d*/
                    'sub_openid'=> $params['openid'],
                ],
            ];
            //更新订单OPENID
            $this->orderModel->where('sn',$params['sn'])->update(['openid'=>$params['openid']]);
            //缓存当前申请支付的临时订单与本订单之前的关系
            $newParams = $this->XpaySignParams($data,$payInfo['mch_key']);
            $data['sign'] = $newParams;
            //构建请求支付接口参数
            $urlParams = str_replace('\\', '', json_encode($data,JSON_UNESCAPED_UNICODE));

            //发起POST请求，获取订单信息
            $result = $this->curlPostJson($urlParams, 'http://openapi.xiangqianpos.com/gateway');
            //缓存请求数据，避免重复请求
            Cache::set('x-'.$params['sn'],$result,600);
        } else {
            $result = Cache::get('x-'.$params['sn']);
        }

        /**********************************下单完成处理的逻辑*************************************************/
        //接收请求下单接口回来的数据
        $newData = json_decode($result,true);
        //计算下单接口返回过来数据的签名
        $newParams1 = $this->XpaySignParams($newData,$payInfo['mch_key']);
        //构建跳转收银台所需要的参数
        $jsonData = [
            'casher_id' => $newData['body']['casher_id'],
            'mch_code'  => $payInfo['mch_code'],
            'third_no'  => $params['sn'],
            'sign'      => ''
        ];
        //
        $cashSign = $this->XpaySignParams($jsonData,$payInfo['mch_key']);
        //构建跳转的参数
        $queryString = 'mch_code='.$payInfo['mch_code'].'&sign='.$cashSign.'&casher_id='.$newData['body']['casher_id'].'&third_no='.$params['sn'];

        // 验证下单接口的签名，如果签名没问题，返回JSON数据跳转收银台，如果有问题则不跳转
        if ($newParams1 == $newData['sign']) {
            //表示验签不成功，直接返回
            //构建json数据
            $url = 'http://open.xiangqianpos.com/wxJsPayV3/casher'.'?'.$queryString;
            header('Location:'.$url);
        } else {
            //表示请求订单验签失败
            echo <<< EOF
            <script>
              alert("支付失败，请重新提交订单");
</script>
EOF;
            die;
        }
    }

    /**
     * 享钱平台获取微信openid
     */
    public function xpayGrant()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        $orderInfo = Cache::get($params['sn']);
        $payInfo = Cache::get($orderInfo['order_ip'].'-xpay_config');
        $url = 'http://open.xiangqianpos.com/wxPayOauth/openid';
        $data = [
            'mch_code'  => $payInfo['mch_code'],
            'charset'   => 'UTF-8',
            'nonce_str' => md5(time()),
            'redirect'  => urlencode($payInfo['pay_domain_1'].'index.php/index/payorder/orderpayment?sn='.$params['sn']),
            'sign'      => '',
        ];
        $data['sign'] = $this->XpaySignParams($data,$payInfo['mch_key']);
        //跳转享钱平台获取openid
        $queryString = 'charset='.$data['charset'].'&mch_code='.$data['mch_code'].'&nonce_str='.$data['nonce_str'].'&redirect='.$data['redirect'].'&sign='.$data['sign'];
        header('Location:'.$url.'?'.$queryString);

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

            $jsApi = <<< EOF
        <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                json_encode($data['jsapi']),
                function(res) {
                if (res.err_msg === "get_brand_wcpay_request:ok") {
                    window.location.href = "/index.php/index/order/orderquery.html"
                } else if (res.err_msg === "get_brand_wcpay_request:cancel") {
                    alert("支付取消");
                }else {
                    alert("支付失败");
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
        </script>
EOF;


        echo $jsApi;
        die;


//            $this->assign('jsApiPrepay', json_encode($data['jsapi']));
//            $this->assign('orderInfo', $data['order_info']);
//            return $this->view->fetch('wechatpay');
//        } else {
//            //表示非法请求
//            die('你请求的支付地址有错误，请重新下单支付');
        }

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
                $payInfo = Cache::get($this->request->ip().'-pay_config');
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
                $payInfo = Cache::get($this->request->ip().'-pay_config');
                $weChatConfig = $this->setConfig($payInfo);
                // 创建接口实例
                $weChat = new Pay($weChatConfig);
                // 组装参数，可以参考官方商户文档
                $options = [
                    'body'              => $orderInfo['production_name'],/*商品名称*/
                    'out_trade_no'      => $params['sn'],/*自己系统的订单号*/
                    'total_fee'         => Env::get('app.debug') ? 1 : $orderInfo['price'] * 100,/*价格，单位：分*/
                    'openid'            => $wxUserInfo['openid'],/*微信网页授权openid*/
                    'trade_type'        => 'JSAPI',/*支付类型，JSAPI--JSAPI支付（或小程序支付）*/
                    'notify_url'        => $payInfo['grant_domain_'.mt_rand(1,3)].'index.php/index/notify/WeChatNotify',/*回调地址,需要指定具体的值*/
                    'spbill_create_ip'  => $this->getClientIp(),
                ];
                //更新订单Openid
                $this->orderModel->isUpdate(true)->where('sn',$orderInfo['sn'])->update(['openid'=>$wxUserInfo['openid']]);
                // 尝试创建订单
                $wxOrder = $weChat->createOrder($options);
                $result = $weChat->createParamsForJsApi($wxOrder['prepay_id']);
                $returnData = [
                    'jsapi' => $result,
                    'order_info' => $orderInfo
                ];
                Cache::set($wxUserInfo['openid'],$returnData);
                //跳转到微信支付
                header('Location:'.$this->payInfo['pay_domain'.mt_rand(1,5)].'index.php/index/payorder/readypay?openid='.$wxUserInfo['openid']);
                // 订单数据处理
            } else {
                //表示非法请求
                die('你请求的支付地址有错误，请重新下单支付');
            }

        } else {
            $this->intoBefore($params);
        }

    }

}