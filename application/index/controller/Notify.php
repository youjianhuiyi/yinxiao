<?php
namespace app\index\controller;

use app\admin\model\sysconfig\Pay as PayModel;
use app\common\controller\Frontend;
use think\Cache;
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
        $notifyUrl = $this->request->domain();
        //通过回调域名反查所属团队
        $payInfo = null;
        for ($i=1;$i<4;$i++) {
            $payInfo = PayModel::where(['notify_url'.$i=>$notifyUrl])->find();
            if ($payInfo) {
                break;
            }
        }
        $teamId = $payInfo->team_id;
        $payStr = $this->getPayInfo($teamId);
        $weChatConfig = $this->setConfig($payStr);
        Cache::set('notify_url',$notifyUrl,600);
        try {
            // 创建接口实例
            $weChat = new Pay($weChatConfig);
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