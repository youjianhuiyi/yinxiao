<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use think\Session;
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
    }

    /**
     * @return string
     * @throws \WeChat\Ex\ceptions\InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \WeChat\Exceptions\InvalidResponseException
     */
    public function index()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        //访问鉴权，如果链接不正确，则直接终止访问
        $this->visited($params);
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
                $payInfo = $this->getPayInfo($paramsNew['tid']);
                $weChatConfig = $this->setConfig($payInfo);
                // 实例接口
                $weChat = new Oauth($weChatConfig);
                // 执行操作
                $wxUserInfo = $weChat->getOauthAccessToken();
                //pay_domain_1缓存，记录支付域名，和支付信息一起，记录当前访问用户与固定一个支付域名绑定，30分钟。
                Cache::set($paramsNew['code'],$wxUserInfo,Env::get('redis.expire'));
                Session::set('openid',$wxUserInfo['openid']);
            }

            //表示已经获取了openid
            //判断链接是否有效
            //TODO::判断逻辑暂时不体现，后期可以使用全局方法进行调用检测
            $data = [
                'openid'    => $wxUserInfo['openid'],/*openid*/
                'pay_domain'=> 'http://pay.ckjdsak.cn/',/*支付域名*/
            ];
            $this->assign('data',$data);
            return $this->view->fetch('shoes');

        } else {
            $this->intoBefore();
        }

    }

}
