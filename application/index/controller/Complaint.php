<?php
namespace app\index\controller;

use app\common\controller\Frontend;

/**
 * 投诉控制器
 * Class Complaint
 * @package app\index\controller
 */
class Complaint extends Frontend
{
    public function index()
    {
        $this->view('tousu')->fetch();
    }
}