<?php

namespace app\common\controller;

use think\Cache;
use think\Controller;
use app\admin\model\sysconfig\Pay as PayModel;
use app\admin\model\sysconfig\Xpay as XPayModel;
use think\Env;
use WeChat\Oauth;
use app\admin\model\Admin as AdminModel;

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
    protected $adminModel = null;
    protected $payModel = null;
    protected $xpayModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->adminModel = new AdminModel();
        $this->payModel = new PayModel();
        $this->xpayModel = new XPayModel();
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
        $str = 'aid='.$data['aid'].'&gid='.$data['gid'].'&tid='.$data['tid'].'&tp='.$data['tp'];
        $code = md5($str);
        return  $code;
    }

    /**
     * 获取链接完整性加密串check_key
     * @param $data
     * @return string
     */
    public function getCheckKey($data)
    {
        $str = 'aid='.$data['aid'].'&check_code='.$data['check_code'].'&gid='.$data['gid'].'&tid='.$data['tid'].'&tp='.$data['tp'];
        $code = md5($str);
        return $code;
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
     * @param $tid integer 团队ID
     * @param $type integer 支付类型 0=微信原生JSAPI支付，1=享钱支付
     * @param $payId integer 支付通道ID
     * @return array|mixed
     */
    public function getPayInfo($type,$payId)
    {
        $userIp = $this->request->ip();
        if ($type == 0) {
            if (!Cache::has($userIp.'-pay_config')) {
                //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
                $userPayData = $this->payModel->get($payId);
                //将支付类型传送进去
                $userPayData['type'] = 0;
                if ($userPayData['is_forbidden'] != 1) {
                    //表示已经被封了
                    //TODO::这里存在一个问题，就是所有支付信息全部有支付管理模块来控制，目前没有做，单独本支付通道被封停后，但是支付配置没有同步数据的问题。
                    //绑定支付配置。如果该用户再次访问，如果有缓存则直接读取。如果没有缓存或者被封，则跳转其他支付
                    Cache::set($userIp.'-pay_config',$userPayData,1800);
                } else {
                    return false;
                }
            } else {
                $userPayData = Cache::get($userIp.'-pay_config');
            }
        } elseif ($type == 1) {
            //表示获享钱支付配置参数
            if (!Cache::has($userIp.'-xpay_config')) {
                //设置缓存-本次记录好缓存，判断是否是支付配置信息记录
                $userPayData = $this->xpayModel->get($payId);
                //将支付类型传送进去
                $userPayData['type'] = 1;
                if ($userPayData) {
                    Cache::set($userIp.'-xpay_config',$userPayData,1800);
                } else {
                    return false;
                }

            } else {
                $userPayData = Cache::get($userIp.'-pay_config');
            }
        } else {
            //TODO::如果所有支付都挂了，可以关闭
            return false;
        }

        return $userPayData;
    }

    /**
     * 获取团队商品缓存记录
     * @param $tid  integer 团队Id
     * @param $gid  integer 商品ID
     * @return mixed
     */
    public function getSelectGoodsInfo($tid,$gid)
    {
        //通过链接获取缓存数据
        if (Cache::has('pro_module?tid='.$tid.'&gid='.$gid)) {
            //表示有缓存数据
            $goodsData = Cache::get('pro_module?tid='.$tid.'&gid='.$gid);
        } else {
            //数据库获取
            $goodsData = $this->selectModel->where(['team_id'=>$tid,'production_id'=>$gid])->find();
            Cache::set('pro_module?tid='.$tid.'&gid='.$gid,$goodsData);
        }
        return $goodsData;
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
     * @param $data
     */
    public function intoBefore($data)
    {
        //第一步，进来先做数据校验
        $paramString = $this->request->query();
        if (!$this->verifyCheckCode($data)) {
            //表示链接被篡改
            die('链接已经被修改，无法访问');
            //TODO:后期可以跳转指定的位置与对应的业务逻辑
        }
        //第二步，获取用户openid与业务员进行绑定，业务员，团队，商品id绑定一个会员。
        $payInfo = Cache::get($this->request->ip().'-pay_config');
        $weChatConfig=$this->setConfig($payInfo);
        //第三步：获取当前aid对应的链接参数携带参数跳转-
        //经过上面的验证，需要对已经验证的链接进行重新组装。
        $checkKey = $this->getCheckKey($data);
        $newParams = $paramString.'&check_key='.$checkKey;
        //TODO:后期可以结合防封域名进行微信授权的跳转
//        $redirect_url = 'http://api.ckjdsak.cn'.$this->request->baseFile().'/index/payorder/wechatgrant'.'?'.$newParams;
        $redirect_url = $payInfo['grant_domain_'.mt_rand(1,3)].$this->request->baseFile().'/index/payorder/wechatgrant'.'?'.$newParams;
        // 实例接口
        $weChat = new Oauth($weChatConfig);
        // 执行操作
        $result = $weChat->getOauthRedirect($redirect_url);
        header('Location:'.$result);
        //第四步：通过防封方式，将参数与页面进行跳转到落地页面
    }


    /**
     * 检测订单是否真实有效
     * @param $data
     */
    public function checkOrderSn($data)
    {
        return ;
    }

    /**
     * xpay签名算法
     * @param $params   array   接口文档里面相关的参数
     * @param $MchKey  string  商户密钥
     * @return array|bool   加密成功返回签名值与原参数数组列表
     */
    public function XpaySignParams($params,$MchKey)
    {
        //按字典序排序数组的键名
        unset($params['sign']);/*剔除sign字段不进行签名算法*/
        ksort($params);
        $string = '';
        if (isset($params['body'])) {
            ksort($params['body']);
            $params['body'] = str_replace("\\/", "/", json_encode($params['body'],JSON_UNESCAPED_UNICODE));
        }
        foreach ($params as $key => $value) {
            $string .= '&'.$key.'='.$value;
        }
        //最后拼接商户号入网的reqKey参数
        $string .= '&key='.$MchKey;
        $ownSign = strtoupper(md5(ltrim($string,'&')));/*执行加密算法*/
        $params['sign'] = $ownSign;/*将签名赋值给数组*/
        return $ownSign;
    }


}
