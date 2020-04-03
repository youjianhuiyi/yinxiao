<?php

namespace app\common\controller;

use think\Cache;
use think\Controller;
use app\admin\model\sysconfig\Pay as PayModel;
use think\Env;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{

    /**
     * 微信支付配置参数
     * @var array
     */
    protected $weChatConfig = [];

    /**
     * 支付宝支付配置参数
     * @var array
     */
    protected $alipayConfig = [];

    /**
     * 支付信息
     */
    protected $payInfo = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 前置授权与判断
     * @comment 用于判断用户的入口链接，以及落地域名的生成。
     * @comment 域名/index.php/index/first?aid=1&tid=1&gid=1&code=xxxx
     * @comment 推广链接生成除域名外，分为三个部分，aid 表示是推广员id，gid表示推广的链接
     * @comment tid表示为所属于团队的ID，code表示校验码，也是唯一值。没有这个值链接就失效，防止用户去改，这个是加密算法的一个值
     * @comment 入口链接进来，获取用户的openid与业务员进行绑定，再跳转到相应的商品链接
     */
    public function intoBefore()
    {
        //第一步，进来先做数据校验
        $params = $this->request->get();
        $newCode = md5('aid='.$params['aid'].'&gid='.$params['gid'].'&tid='.$params['tid']);
        if ($params['code'] != $newCode) {
            //表示链接被篡改
            die('链接已经被修改，无法访问');
            //TODO:后期可以跳转指定的位置与对应的业务逻辑
        }
        //第二步，获取用户openid与业务员进行绑定，业务员，团队，商品id绑定一个会员。
        $this->payInfo = PayModel::where(['team_id'=>$params['tid']])->find();
        //设置缓存
        Cache::set('pay_info_'.$params['tid'],$this->payInfo,Env::get('redis.expire'));
        //第三步：获取携带参数跳转

        //第四步：通过防封方式，将参数与页面进行跳转到落地页面

    }

}
