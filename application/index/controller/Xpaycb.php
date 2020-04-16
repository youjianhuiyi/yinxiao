<?php
namespace app\index\Controller;

use think\Cache;
use think\Controller;

/**
 * 支付加密算法
 * Class Xpaycb
 */
class Xpaycb extends Controller
{
    /**
     * 提交订单，请求支付接口
     * @return void
     */
    public function submitOrder()
    {
        $data = [
            'ticket'    => '1000',/*用来匹配请求*/
            //支付宝pay.alipay.native,微信pay.wxpay.native,京东pay.jdpay.native,qq pay.qqpay.native,银联二维码 pay.unionpay.native
            'service'   => 'pay.wxpay.native',
            'version'   => '2.0',/*版本号 默认是2.0*/
            'sign_type' => 'MD5',/*签名方式，默认是md5*/
            'mch_code'  =>  '621140010001',/*商户号 享多多系统的门店编码*/
            'timestamp' => date('YmdHis',time()),/*时间戳 发送请求的时间，格式"yyyyMMddHHmmss"*/
            'sign'      => '',/*签名*/
            //'channel_code'  =>  '',/*渠道编号 不是必填项目*/
            //业务数据 Json格式的数据
            'body'      => [
                'orderNo'       => '2020040161224520000200002368',/*商户订单号 商户系统内部的订单号 ,32个字符内、 可包含字母,确保在商户系统唯一*/
                //'device'        => '',/*设备号 终端设备号     不是必填*/
                'order_info'    => '花花公子-鞋子',/*商品描述*/
                //'attach'        => '',/*商户附加信息，可做扩展参数     不是必填*/
                'total_amount'  => '1',/*总金额，以分为单位，不允许包含任何字、符号*/
                //'undiscount_amount' =>  '',/*不参与折扣金额       不是必填*/
                'mch_create_ip' => '127.0.0.1',/*订单生成的机器 IP*/
                'notify_url'    => 'http://notify.ckjdsak.cn/index.php/index/notify/xpayNotify',
                //'goods_tag'     => '',/*商品标记，用于优惠券或者满减使用  不是必填*/
                //'time_start'    => '',/*订单生成时间 订单生成时间，格式为yyyymmddhhmmss，如2009年12月25日9点10分10秒表示为20091225091010。时区为GMT+8 beijing。该时间取自商户服务器*/
                //'time_expire'   => '',/*订单超时时间 订单失效时间，格式为yyyymmddhhmmss，如2009年12月27日9点10分10秒表示为20091227091010。时区为GMT+8 beijing。该时间取自商户服务器*/
                //'option_user'   => '',/*操作员id(享多多系统的营业员id)*/
                //'extend_params' => ''/*业务扩展参数()*/
            ],
        ];


        $newParams = $this->signParams($data);
        $data['sign'] = $newParams;
//        dump($newParams);die;
        //构建请求支付接口参数
        $urlParams = str_replace('\\', '', json_encode($data,JSON_UNESCAPED_UNICODE));
        dump($urlParams);
        //发起POST请求，获取订单信息
//        dump($urlParams);die;
        $result = $this->curlPost($urlParams, 'http://openapi.xiangqianpos.com/gateway');
//        $result = $this->curlPost($urlParams, 'http://openapi.cs.xiangqianpos.com/gateway');
        //构建页面展示需要的数据
        $data = json_decode($result,true);
        Cache::set('xpay_pay',$result);
        dump($data);
        //判断请求响应回来的数据与验签
        if ($data['status'] == 0) {
            //请求后验签
            //响应回来的数据签名算法
//            $ownData = self::signParams($data);
            //核对响应回来的签名报文与自己的算法报文是否一致
//            if ($ownData['sign'] !== $data['sign']) {
                //如果不一样，则直接返回false
//                return false;
//            }
        }
        //返回响应的
//        var_dump($data);die;
        //以json返回，后面需要开发者根据自己的业务逻辑进行页面渲染
//        return json_encode($data);
    }


    /**
     * 签名算法
     * @param $params   array   接口文档里面相关的参数
     * @return array|bool   加密成功返回签名值与原参数数组列表
     */
    public function signParams($params)
    {
        //按字典序排序数组的键名
        unset($params['sign']);/*剔除sign字段不进行签名算法*/
        ksort($params);
        $string = '';
        ksort($params['body']);
        $params['body'] = str_replace("\\/", "/", json_encode($params['body'],JSON_UNESCAPED_UNICODE));
        foreach ($params as $key => $value) {
            $string .= '&'.$key.'='.$value;
        }
        //最后拼接商户号入网的reqKey参数
        $string .= '&key=UNkXjme81w8o2dUmVqOB1w==';
        $ownSign = strtoupper(md5(ltrim($string,'&')));/*执行加密算法*/
        $params['sign'] = $ownSign;/*将签名赋值给数组*/
        return $ownSign;
    }


    /**
     * CURL_POST请求
     * @param $str  string  json字符串
     * @param $url  string  请求的url地址
     * @param $second  int  请求最长时间
     * @return bool|string
     */
    public static function curlPost($str, $url, $second = 30)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($str)));
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            echo "curl 出错，错误码:$error" . "<br>";
            return false;
        }
    }

}
