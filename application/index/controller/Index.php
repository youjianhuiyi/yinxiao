<?php

namespace app\index\controller;

use app\common\controller\Frontend;


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
        $this->intoBefore();
    }
    /**
     * @return string
     * @throws \think\Exception
     */
    public function index()
    {

        //构建需要生成的模块参数
        $data = [

        ];
        $this->assign('data',$data);
        return $this->view->fetch('shoes');
    }

}
