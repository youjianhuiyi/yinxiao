<?php

namespace app\admin\controller\data;

use app\common\controller\Backend;

/**
 * 数据分析管理
 *
 * @icon fa fa-circle-o
 */
class Analysis extends Backend
{
    
    /**
     * Analysis模型对象
     * @var \app\admin\model\data\Analysis
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\data\Analysis;

    }

    /**
     * 首页
     * @return \think\response\Json|void
     */
    public function index()
    {
        die('此功能已经转移！');
    }

    /**
     * 添加
     */
    public function add()
    {
        die('不能手动进行添加');
    }

    /**
     * 编辑
     * @param null $ids
     */
    public function edit($ids = null)
    {
        die('不能手动进行编辑');
    }
}
