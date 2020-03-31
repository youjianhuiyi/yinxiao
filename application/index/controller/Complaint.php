<?php
namespace app\index\controller;

use think\Controller;

/**
 * 投诉控制器
 * Class Complaint
 * @package app\index\controller
 */
class Complaint extends Controller
{
    public function index()
    {
        $this->view('tousu')->fetch();
    }
}