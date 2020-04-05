<?php

namespace app\index\controller;

use app\admin\model\sysconfig\Pay as PayModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use WeChat\Oauth;


/**
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    public function _initialize()
    {
        parent::_initialize();
        //执行前置方法，
//        $this->intoBefore();
    }
    /**
     * @return string
     * @throws \think\Exception
     */
    public function index()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (isset($params['code']) && !empty($params['code'])) {

            try {
                if (!Cache::has('pay_info_' . $params['tid'])) {
                    //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
                    $this->payInfo = PayModel::where(['team_id' => $params['tid']])->find()->toArray();
                    Cache::set('pay_info_' . $params['tid'], $this->payInfo, Env::get('redis.expire'));
                } else {
                    $this->payInfo = Cache::get('pay_info_' . $params['tid']);
                }
                $this->weChatConfig = $this->setConfig($this->payInfo);
                // 实例接口
                $weChat = new Oauth($this->weChatConfig);
                // 执行操作
                $wxUserInfo = $weChat->getOauthAccessToken();

                //pay_domain_1缓存，记录支付域名，和支付信息一起，记录当前访问用户与固定一个支付域名绑定，30分钟。

                //表示已经获取了openid
                //判断链接是否有效
                //TODO::判断逻辑暂时不体现，后期可以使用全局方法进行调用检测
                $data = [
                    'openid'    => $wxUserInfo['openid'],/*openid*/
                    'pay_domain'=> 'http://pay.ckjdsak.cn/',/*支付域名*/
                ];
                $this->assign('data',$data);
                return $this->view->fetch('shoes');

            } catch (\Exception $e) {
                // 异常处理
                echo $e->getMessage();
            }

        } else {

            $this->intoBefore();
        }

    }

}
