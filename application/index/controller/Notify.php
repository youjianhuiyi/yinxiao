<?php
namespace app\index\controller;

use app\admin\model\sysconfig\Pay as PayModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use WeChat\Pay;

/**
 * 回调处理类
 * Class Notify
 * @package app\index\controller
 */
class Notify extends Frontend
{

    public function WeChatNotify()
    {
        Cache::set('request',$this->request->request(),600);
        Cache::set('global',$GLOBALS,600);

//        $payInfo = $this->getPayInfo($tid);
//        $weChatConfig = $this->setConfig($payInfo);
//
//        try {
//            // 创建接口实例
//            $weChat = new Pay($weChatConfig);
//
//            // 尝试创建订单
//            $result = $weChat->getNotify();
//
//            Cache::set('order_notify',$result,600);
//            // 订单数据处理
//            var_export($result);
//
//        } catch(\Exception $e) {
//
//            // 出错啦，处理下吧
//            echo $e->getMessage() . PHP_EOL;
//
//        }
    }


}