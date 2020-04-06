<?php
namespace app\index\controller;

use app\admin\model\order\Order as OrderModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 回调处理类
 * Class Notify
 * @package app\index\controller
 */
class Notify extends Frontend
{
    /**
     * 签名算法
     * @param $params   array   接口文档里面相关的参数
     * @param $mchKey  string  商户支付密钥Key值
     * @return array|bool   加密成功返回签名值与原参数数组列表
     */
    public function signParams($params,$mchKey)
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
            $string .= '&key='.$mchKey;
        } else {
            return false;
        }
        $ownSign = strtoupper(md5(ltrim($string,'&')));/*执行加密算法*/
        $params['sign'] = $ownSign;/*将签名赋值给数组*/
        return $params;
    }

    /**
     * 解析XML内容到数组
     * @param string $xml
     * @return array
     */
    public function xml2arr($xml)
    {
        $entity = libxml_disable_entity_loader(true);
        $data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_disable_entity_loader($entity);
        return json_decode(json_encode($data), true);
    }

    public function WeChatNotify()
    {
        $result = $this->xml2arr(file_get_contents('php://input'));
        Cache::set('sign',$result['sign']);
        //通过回调的信息反查订单相关信息
        $orderInfo = OrderModel::where(['sn'=>$result['out_trade_no']])->find()->toArray();
        Cache::set('no_order',$orderInfo);
        $payInfo = $this->getPayInfo($orderInfo['team_id']);
        // 创建接口实例
//        [appid]=>wx90588380da4a2bb0
//        [bank_type]=>OTHERS
//        [cash_fee]=>1
//        [fee_type]=>CNY
//        [is_subscribe]=>Y
//        [mch_id]=>1583492131
//        [nonce_str]=>nrdclxldx8r05pi555dw7o51gqa0vxgr
//        [openid]=>of5TOwBkJC0jSnth-D20xiL1W_i4
//        [out_trade_no]=>2020040600445700003000026634
//        [result_code]=>SUCCESS
//        [return_code]=>SUCCESS
//        [sign]=>A654BED136BDA766BB930D9C4ED124CF
//        [time_end]=>20200406004505
//        [total_fee]=>1
//        [trade_type]=>JSAPI
//        [transaction_id]=>4200000495202004060198644235
        // 先回调验签
        $newSign = $this->signParams($result,$payInfo['mch_key']);
        Cache::set('new_sign',$newSign);
        if ($result['sign'] === $newSign) {
            //表示验签成功
            Cache::set('sign','ok');
            $data  = [
                'id'            => $orderInfo['id'],
                'transaction_id' => $result['transaction_id'],/*微信支付订单号*/
//                        'openid'         => $result['openid'],/*购买者的openid，进行支付的时候进行写入，与支付链接绑定起来*/
                'pay_type'       => 0,/*支付类型，0=微信，1=支付宝*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];

            //更新数据
            Db::startTrans();
            try {
                OrderModel::isUpdate(true)->save($data);
                Cache::set('update','ok');
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }

            //返回成功
            $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            echo $str;
            return ;
        } else {
            //返回失败
            $str = '<xml><return_code><![CDATA[fail]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>';
            echo $str;
            return ;
        }

    }

}