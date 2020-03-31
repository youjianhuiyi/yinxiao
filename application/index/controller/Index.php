<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        $data = [

        ];
        $this->assign('data',$data);
        return $this->view->fetch('shoes');
    }

}
