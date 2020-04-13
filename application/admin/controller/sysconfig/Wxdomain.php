<?php

namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;

/**
 * 微信域名管理
 *
 * @icon fa fa-circle-o
 */
class Wxdomain extends Backend
{
    
    /**
     * Wxdomain模型对象
     * @var \app\admin\model\sysconfig\Wxdomain
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Wxdomain;

    }



}
