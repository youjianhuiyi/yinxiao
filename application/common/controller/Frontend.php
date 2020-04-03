<?php

namespace app\common\controller;

use think\Controller;
use think\Validate;

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

}
