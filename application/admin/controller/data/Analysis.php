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


}
