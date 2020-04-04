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
        if (!Cache::has('pay_info_1')) {
            //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
            $this->payInfo = PayModel::where(['team_id'=>1])->find()->toArray();
            Cache::set('pay_info_1',$this->payInfo,Env::get('redis.expire'));
        } else {
            $this->payInfo = Cache::get('pay_info_1');
        }

        $this->weChatConfig = $this->setConfig($this->payInfo);

        try {
            // 创建接口实例
            $weChat = new Pay($this->weChatConfig);

            // 尝试创建订单
            $result = $weChat->getNotify();

            Cache::set('order_notify',$result,600);
            // 订单数据处理
            var_export($result);

        } catch(\Exception $e) {

            // 出错啦，处理下吧
            echo $e->getMessage() . PHP_EOL;

        }
    }


}