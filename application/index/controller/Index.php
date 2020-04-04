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
            $this->intoBefore();
        } else {
            dump($params);
            try {
                if (!Cache::has('pay_info_'.$params['tid'])) {
                    //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
                    $this->payInfo = PayModel::where(['team_id'=>$params['tid']])->find()->toArray();
                    Cache::set('pay_info_'.$params['tid'],$this->payInfo,Env::get('redis.expire'));
                } else {
                    $this->payInfo = Cache::get('pay_info_'.$params['tid']);
                }
                $this->weChatConfig=$this->setConfig($this->payInfo);
                // 实例接口
                $weChat = new Oauth($this->weChatConfig);
                // 执行操作
                $result = $weChat->getOauthAccessToken();
                dump($this->request->url(true));
                dump($_GET);
                dump($result);
            } catch (\Exception $e){
                // 异常处理
                echo  $e->getMessage();
            }
        }

        die;

        if (isset($params['openid']) && !empty($params['openid'])) {
            //表示已经获取了openid
            //第一步。判断链接是否有效
            if (isset($params['check_code']) && !empty($params['check_code'])) {
                //表示可能已经经过微信授权。再继续判断此key的真实性
                $str = 'aid='.$params['aid'].'&gid='.$params['gid'].'&tid='.$params['tid'];
                $newCheckCode = md5($str);
                if ($newCheckCode == $params['check_code']) {

                    if (isset($params['check_key']) && !empty($params['check_key'])) {
                        //表示可能已经经过微信授权。再继续判断此key的真实性
                        $str = 'aid='.$params['aid'].'&gid='.$params['gid'].'&tid='.$params['tid'].'&check_code='.$params['check_code'];
                        $newCheckKey = md5($str);
                        if ($newCheckKey == $params['check_key']) {
                            //表示直接放行，跳转到指定落地页面链接，
                            //构建需要生成的模块参数

                            $data = [

                            ];
                            $this->assign('data',$data);
                            return $this->view->fetch('shoes');
                        } else {
                            //表示需要再判断链接是否有效
                            exit('链接已经被恶意修改，请重新访问');
                            //TODO：暂时直接断开程序，后期可以进行拓展，先处理成功的方案 4-4
                        }

                    } else {
                        //表示没有微信授权
                        $this->intoBefore();
                    }
                } else {
                    exit('此链接不是你正确的推广链接');
                }

            } else {
                //表示此链接是直接访问此方法的路由，直接拒绝，避免产生不必要的结果
                exit('请携带正确的参数访问网站，不要这么直接');
                //TODO:防止用户直接不带任何参数访问落地页面，产生不必要的报错，后期可以进行规避和跳转 4-4
            }
        }

    }

}
