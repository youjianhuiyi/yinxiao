<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use app\admin\model\production\Production_select as SelectModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\sysconfig\Consumables as ConsumablesModel;
use app\admin\model\sysconfig\Ground as GroundModel;
use app\admin\model\sysconfig\Payset as PaySetModel;

/**
 * 模板渲染
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    protected $selectModel = null;
    protected $urlModel = null;
    protected $groundModel = null;
    protected $consumablesModel = null;
    protected $paysetModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->selectModel = new SelectModel();
        $this->urlModel = new UrlModel();
        $this->groundModel = new GroundModel();
        $this->consumablesModel = new ConsumablesModel();
        $this->paysetModel = new PaySetModel();
    }

    public function test()
    {
//        $payPool = collection($this->paysetModel->where(['team_id'=>12])->select())->toArray();
//        dump($payPool);die;
//        $this->getPayChannel($payPool);
    }

    public function index()
    {
        
    }

    /**
     * 微信支付落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index0()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            die("请使用正确的链接进行访问！！");
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            die("请不要使用非法手段更改链接");
        }

        $userInfo = $this->adminModel->get($params['aid']);

        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);

        //获取访问者IP
        $userIp  = $this->request->ip();

        //TODO::这里直接取redis的值，因为刚刚403跳转前一秒，已经写入缓存了，直接取即可，不过这操作还是有点不稳当
        if (Cache::get($userIp.'-pay_config') === false) {
            $payInfo = Cache::get($userIp.'-xpay_config');
        } else {
            $payInfo = Cache::get($userIp.'-pay_config');
        }
        if (!$payInfo) {
            //表示支付全挂了。
            die('请检查支付通道是否正常~~~');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'phone1'    => $goodsData['phone1'],
            'phone2'    => $goodsData['phone2'],
            'tongji'    => $goodsData['tongji'],
            'pay_type'  => 0,//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'sales_price'     => $goodsData['sales_price'],//支付价格（必填）
            'discount'     => $goodsData['discount'],//支付价格（必填）
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['pay_domain'.mt_rand(1,5)],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        => $payInfo['grant_domain_'.mt_rand(1,3)]//订单提交成功后跳转链接支付链接（跳转之前先调用微信授权，再落地到支付界面，这中间，需要将重要的参数通过url参数传送）
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        Cache::set('index_'.$params['check_code'],$data);/*缓存好数据。用于后面调用数据*/
        //更新访问记录。
        $this->urlModel->where(['check_code'=>$params['check_code']])->setInc('count');

        $this->assign('data',$data);
        return $this->view->fetch($params['tp']);
    }

    /**
     * xpay落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index1()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            die("请使用正确的链接进行访问！！");
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            die("请不要使用非法手段更改链接");
        }

        $userInfo = $this->adminModel->get($params['aid']);

        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);

        //获取访问者IP
        $userIp  = $this->request->ip();

        //TODO::这里直接取redis的值，因为刚刚403跳转前一秒，已经写入缓存了，直接取即可，不过这操作还是有点不稳当
        if (Cache::get($userIp.'-pay_config') === false) {
            $payInfo = Cache::get($userIp.'-xpay_config');
        } else {
            $payInfo = Cache::get($userIp.'-pay_config');
        }
        if (!$payInfo) {
            //表示支付全挂了。
            die('请检查支付通道是否正常~~~');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'phone1'    => $goodsData['phone1'],
            'phone2'    => $goodsData['phone2'],
            'tongji'    => $goodsData['tongji'],
            'pay_type'  => 1,//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'sales_price'     => $goodsData['sales_price'],//支付价格（必填）
            'discount'     => $goodsData['discount'],//支付价格（必填）
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['api_url'],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        Cache::set('index_'.$params['check_code'],$data);/*缓存好数据。用于后面调用数据*/
        //更新访问记录。
        $this->urlModel->where(['check_code'=>$params['check_code']])->setInc('count');
        $this->assign('data',$data);
        return $this->view->fetch($params['tp']);
    }


    /**
     * 最终落地页面,403请求接口
     */
    public function loadGround()
    {
        //获取请求接口允许的域名列表
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with,X_Requested_With,Origin,application/json'); // 设置允许自定义请求头的字段
        //接收403页面来的参数请求
        $params = $this->request->param();
        //表示验签参数可能有效，接下来进行验证
        if (Cache::has($params['code'])) {
            $queryStr = Cache::get($params['code']);
        } else {
            $queryStr = $this->urlModel->where(['check_code'=>$params['code']])->find()['query_string'];
        }
        $str = md5(explode('&check_code',$queryStr)[0]);
        $condition = $this->do403Params($queryStr);
        //查询当前团队使用的是哪种支付方式,通过渲染方式不同，落地方法不一样，流程不一样。
        //根据推广链接403入口，来决定是走哪种支付方式，不同的支付方式，需要不同地流程与渲染，落地，成交，支付
        $payPool = collection($this->paysetModel->where(['team_id'=>$condition['tid']])->select())->toArray();
        //根据查询出来的数据，生成支付通道。
        $payInfo = $this->getPayChannel($payPool);
        if (false === $payInfo) {
            //表示没有支付
            die("支付通道无效，请联系老板！！！");
        }

        //对参数进行验证
        if ($str === $params['code']) {
            //表示验证成功，获取炮灰域名准备落地
            $consumables = $this->consumablesModel->where(['is_forbidden'=>0,'is_rand'=>0])->column('domain_url');
            if (count($consumables) >= 1) {
                $luckDomain = array_pop($consumables);
                //TODO::更改域名为正在使用状态
            } else {
                //表示没有炮灰域名了
                $luckDomain = 'http://www.qq.com';
            }
            //根据不同的支付类型，跳转不同的支付方法与落地页面
            $wholeDomain = 'http://'.time().'.'.$luckDomain.'/index.php/index/index/index'.$payInfo['type'].'?'.$queryStr;
            Cache::set('whole_domain_1',$wholeDomain);
            echo "handler('successcode','{$wholeDomain}')";
            die;
        } else {
            //表示验证失败
            echo "handler('failure','http://www.qq.com')";
            die;
        }

    }


    /**
     * 处理403的参数
     * @remark 返回403解密后的参数数组。用于查询数据
     * @param $data string 通过403解密获取的字段参数
     * @return array
     */
    public function do403Params($data)
    {
        //$data = aid=21&gid=4&tid=12&tp=shoes2&check_code=6c2cca0d880648d025948c7ffd57aea1
        $arr = explode('&', $data);
        $newArr = [];
        foreach ($arr as $value) {
            $tmp = explode('=', $value);
            $newArr[$tmp[0]] = $tmp[1];
        }
//        [
//            'aid'   => 21,
//            'gid'   => 4,
//            'tid'   => 12,
//            'tp'    => 'shoes',
//            'check_coed'=> '6c2cca0d880648d025948c7ffd57aea1'
//        ]
        return $newArr;
    }


    /**
     * 根据支付池里面的数据，生成支付参数
     * @param $data
     * @return bool|array
     */
    public function getPayChannel($data)
    {
        if (!empty($data)) {
            if (count($data) == 1) {
                //表示只有一个支付通道
                return $this->getPayInfo($data[0]['type'],$data[0]['pay_id']);
            } else {
                //表示有多个支付通道，进行随机抽取。
                $res =  $data[mt_rand(0,count($data)-1)];
                return $this->getPayInfo($res['type'],$res['pay_id']);
            }
        } else {
            //表示已经没有支付通道使用了
            return false;
        }
    }

}
