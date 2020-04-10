<?php

namespace app\common\controller;

use think\Cache;
use think\Controller;
use app\admin\model\sysconfig\Pay as PayModel;
use think\Env;
use WeChat\Oauth;

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
     * 初始化微信支付配置参数基类
     * @param $data
     * @return array
     */
    public function setConfig($data)
    {
        return $weChatConfig = [
            'token'          => isset($data['token']) && !empty($data['token']) ? $data['token'] : '',
            'appid'          => $data['app_id'],
            'appsecret'      => $data['app_secret'],
            'encodingaeskey' => isset($data['encodingaeskey']) && !empty($data['encodingaeskey']) ? $data['encodingaeskey'] : '',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            'mch_id'         => $data['mch_id'],
            'mch_key'        => $data['mch_key'],
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => '',
            'ssl_cer'        => '',
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => APP_PATH.'/runtime/pay/',
        ];
    }

    /**
     * 获取加密算法check_code
     * @param $data
     * @return string
     */
    public function getCheckCode($data)
    {
        return  md5('aid='.$data['aid'].'&gid='.$data['gid'].'&tid='.$data['tid'].'&tp='.$data['tp']);
    }

    /**
     * 获取链接完整性加密串check_key
     * @param $data
     * @return string
     */
    public function getCheckKey($data)
    {
        return  md5('aid='.$data['aid'].'&check_code='.$data['check_code'].'&gid='.$data['gid'].'&tid='.$data['tid'].'&tp='.$data['tp']);
    }

    /**
     * 检验链接是否完整
     * @param $data
     * @return bool
     */
    public function verifyCheckCode($data)
    {
        $newCode = $this->getCheckCode($data);
        return $data['check_code'] == $newCode ? true : false;
    }

    /**
     * 检验微信授权后的链接是否完整
     * @param $data
     * @return bool
     */
    public function verifyCheckKey($data)
    {
        $newKey = $this->getCheckKey($data);
        return $data['check_key'] == $newKey ? true : false;
    }

    /**
     * 获取团队支付信息
     * @param $tid
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPayInfo($tid)
    {
        if (!Cache::has('pay_info_'.$tid)) {
            //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
            $payInfo = PayModel::where(['team_id'=>$tid])->find()->toArray();
            Cache::set('pay_info_'.$tid,$payInfo,Env::get('redis.expire'));
        } else {
            $payInfo = Cache::get('pay_info_'.$tid);
        }
        return $payInfo;
    }

    /**
     * 检测用户方法的请求是否合法
     * @param $data
     */
    public function visited($data)
    {
        if (empty($data)) {
            //表示直接访问链接。
            die("请访问正确的链接，不要随意改动链接！！！");
        }
        //如果链接已经生成
        $res = $this->verifyCheckCode($data);
        if (!$res) {
            die("你访问的链接已经变动，不要随意更改链接~~~");
        }
    }

    /**
     * 前置授权与判断
     * @comment 用于判断用户的入口链接，以及落地域名的生成。
     * @comment 域名/index.php/index/first?aid=1&tid=1&gid=1&check_code=xxxx
     * @comment 推广链接生成除域名外，分为三个部分，aid 表示是推广员id，gid表示推广的链接
     * @comment tid表示为所属于团队的ID，check_code表示校验码，也是唯一值。没有这个值链接就失效，防止用户去改，这个是加密算法的一个值
     * @comment 入口链接进来，获取用户的openid与业务员进行绑定，再跳转到相应的商品链接
     */
    public function intoBefore()
    {
        //第一步，进来先做数据校验
        $params = $this->request->param();
        $paramString = $this->request->query();
        if (!$this->verifyCheckCode($params)) {
            //表示链接被篡改
            die('链接已经被修改，无法访问');
            //TODO:后期可以跳转指定的位置与对应的业务逻辑
        }
        //第二步，获取用户openid与业务员进行绑定，业务员，团队，商品id绑定一个会员。
        $payInfo = $this->getPayInfo($params['tid']);
        $weChatConfig=$this->setConfig($payInfo);
        //第三步：获取当前aid对应的链接参数携带参数跳转-
        //经过上面的验证，需要对已经验证的链接进行重新组装。
        $checkKey = $this->getCheckKey($params);
        $newParams = $paramString.'&check_key='.$checkKey;
        //TODO:后期可以结合防封域名进行微信授权的跳转
        $redirect_url = $this->request->domain().$this->request->baseFile().'/index/index/index'.'?'.$newParams;
        // 实例接口
        $weChat = new Oauth($weChatConfig);
        // 执行操作
        $result = $weChat->getOauthRedirect($redirect_url);
        header('Location:'.$result);
        //第四步：通过防封方式，将参数与页面进行跳转到落地页面
    }




}
