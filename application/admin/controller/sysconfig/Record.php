<?php

namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;

/**
 * 商户收款记录
 *
 * @icon fa fa-circle-o
 */
class Record extends Backend
{
    /**
     * Record模型对象
     * @var \app\admin\model\sysconfig\Record
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Record;
    }

    public function add()
    {
        $this->error('不能进行添加操作');
    }

    public function edit($ids = null)
    {
        $this->error('不能进行编辑操作');

    }

    public function del($ids = null)
    {
        $this->error('不能进行删除操作');
    }
}
