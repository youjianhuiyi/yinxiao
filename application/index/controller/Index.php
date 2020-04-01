<?php

namespace app\index\controller;

use app\common\controller\Frontend;


/**
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

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
