<?php
/**
 * author : yulinzhihou
 * e-mail : yulinzhihou@gmail.com
 * date   : 2019-05-25 14:04
 */


/**
 * 支付加密算法
 * C扫B demo
 * Class PaySignCB
 */
class PaySignCB
{
    //声明静态属性，用于存储接口文档里面需要用来签名报文的字段
    public static $params = null;

    /**
     * 构造方法
     * PaySignController constructor.
     */
    public function __construct()
    {
        //初始化静态属性
        self::$params = [
            'currency'  => 'cny',/*币种*/
            'amount'    => 100,/*支付金额*/
            'clientIp'  => '127.0.0.1',/*客户端IP*/
            'mchOrderNo'=> 'TLBB20190525003',/*商户订单号*/
            'extra'     => ['total_amount'=>100,'goods_tag'=>'goods_tag特定渠道发起时额外参数'],/*附加参数,特定渠道发起时额外参数,*/
            'mchId'     => '123456',/*商户ID*/
            'body'      => '这里是商品描述信息',/*商品描述信息*/
            'requestTimestamp'=>self::getMillisecond(),/*请求时间*/
            'device'    => '123123',/*设备号*/
            'returnUrl' => 'https://www.baidu.com',/*网页跳转地址,可以不填写*/
            'channelId' => 'NEWLAND_ALIPAY_QR',/*渠道ID，根据接口文档里面的需求进行更改*/
            'sign'      => '',/*签名值*/
            'param1'    => '',/*扩展参数1 支付中心回调时会原样返回*/
            'subject'   => '这里面是商品主题',/*商品主题*/
            'param2'    => '',/*扩展参数2 支付中心回调时会原样返回*/
            'notifyUrl' => 'http://tlbb3d.test/index/callback/notifyX',/*支付结果回调URL*/
        ];
    }

    /**
     * 提交订单，请求支付接口
     * @return bool
     */
    public function submitOrder()
    {
        $newParams = self::signParams(self::$params);
        //构建请求支付接口参数
        $urlParams = ['params'=>str_replace('\\', '', json_encode($newParams,JSON_UNESCAPED_UNICODE))];
        //发起POST请求，获取订单信息
        $result = self::curlPost($urlParams, 'http://118.24.27.93:11180/payment/api/pay/trade_create');
        //构建页面展示需要的数据
        $data = json_decode($result,true);
        //判断请求响应回来的数据与验签
        if ($data['retCode'] == 'SUCCESS') {
            //请求后验签
            //响应回来的数据签名算法
            $ownData = self::signParams($data);
            //核对响应回来的签名报文与自己的算法报文是否一致
            if ($ownData['sign'] !== $data['sign']) {
                //如果不一样，则直接返回false
                return false;
            }
        }
        //返回响应的
        var_dump($data);die;
        //以json返回，后面需要开发者根据自己的业务逻辑进行页面渲染
        return json_encode($data);
    }


    /**
     * 获取时间戳到毫秒
     * @return bool|string
     * 获取当前时间需要确定php.ini环境里面设置的地区为date.timezone = PRC,
     * 或者手动在当前文件开头 date_default_timezone_set('PRC');
     */
    public static function getMillisecond()
    {
        list($mSec, $sec) = explode(' ', microtime());
        $mSecTime =  (float)sprintf('%.0f', (floatval($mSec) + floatval($sec)) * 1000);
        return $mSecTime = substr($mSecTime,0,13);
    }


    /**
     * 签名算法
     * @param $params   array   接口文档里面相关的参数
     * @return array|bool   加密成功返回签名值与原参数数组列表
     */
    public static function signParams($params)
    {
        //按字典序排序数组的键名
        unset($params['sign']);/*剔除sign字段不进行签名算法*/
        ksort($params);
        $string = '';
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $string .= '&'.$key.'='.json_encode($value,JSON_UNESCAPED_UNICODE);
                } elseif ($value && !empty($value)) {
                    $string .= '&'.$key.'='.$value;
                }
            }
            //最后拼接商户号入网的reqKey参数
            $string .= '&key=afdfadsfdsafdsafdsafasd';
        } else {
            return false;
        }
        $ownSign = strtoupper(md5(ltrim($string,'&')));/*执行加密算法*/
        $params['sign'] = $ownSign;/*将签名赋值给数组*/
        return $params;
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
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置 header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post 提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        //运行 curl
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
//demo调用。里面的商户参数和商品订单参数 ，需要根据自己的实际情况进行存储
//$obj = new PaySignController();
//$obj->submitOrder();