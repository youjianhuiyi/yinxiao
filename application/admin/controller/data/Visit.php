<?php

namespace app\admin\controller\data;

use app\common\controller\Backend;

/**
 * 访问记录
 *
 * @icon fa fa-circle-o
 */
class Visit extends Backend
{
    
    /**
     * Visit模型对象
     * @var \app\admin\model\data\Visit
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\data\Visit;
    }

    /**
     * 查看详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function detail($ids=null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }
}
