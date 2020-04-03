<?php
namespace app\index\controller;

use app\common\controller\Frontend;
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
        try {
            // 创建接口实例
            $weChat = new Pay($this->weChatConfig);

            // 尝试创建订单
            $result = $weChat->getNotify();

            // 订单数据处理
            var_export($result);

        } catch(Exception $e) {

            // 出错啦，处理下吧
            echo $e->getMessage() . PHP_EOL;

        }
    }


}