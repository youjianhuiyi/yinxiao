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
    protected $orderModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();

    }
    /**
     * 签名算法
     * @param $params   array   接口文档里面相关的参数
     * @param $mchKey  string  商户支付密钥Key值
     * @return string   加密成功返回签名值与原参数数组列表
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
        return strtoupper(md5(ltrim($string,'&')));/*执行加密算法*/
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
        //通过回调的信息反查订单相关信息
        $orderInfo = $this->orderModel->where(['sn'=>$result['out_trade_no']])->find()->toArray();
        //根据订单数据提取支付信息
        $payInfo = Cache::get($orderInfo['order_ip'].'-pay_config');
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
        if ($result['sign'] === $newSign) {
            //表示验签成功
            $data  = [
                'id'             => $orderInfo['id'],
                'transaction_id' => $result['transaction_id'],/*微信支付订单号*/
                'nonce_str'      => $result['nonce_str'],
                'pay_type'       => 0,/*支付类型，0=微信，1=享钱*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];
            //更新数据
            Db::startTrans();
            try {
                (new OrderModel())->isUpdate(true)->save($data);
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

    public function xpayNotify()
    {
        $returnData = file_get_contents('php://input');
        $data = json_decode($returnData);
        Cache::set('x_notify_return',$returnData);
        //通过回调的信息反查订单相关信息
        //通过临时订单查找真实订单号，
        $tmpNo = Cache::get($data['orderNo']);
        $orderInfo = Cache::get($tmpNo);
        //根据订单数据提取支付信息
        $payInfo = Cache::get($orderInfo['order_ip'].'-xpay_config');
        // 先回调验签
        $newSign = $this->signParams($data,$payInfo['mch_key']);
        if ($data['sign'] === $newSign) {
            //表示验签成功
            $data  = [
                'id'             => $orderInfo['id'],
                'transaction_id' => $data['trade_no'],/*微信支付订单号*/
                'nonce_str'      => $data['nonce_str'],
                'pay_type'       => 1,/*支付类型，0=微信，1=享钱*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
                'xdd_trade_no'   => $data['xdd_trade_no'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];
            //更新数据
            Db::startTrans();
            try {
                $this->orderModel->isUpdate(true)->save($data);
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