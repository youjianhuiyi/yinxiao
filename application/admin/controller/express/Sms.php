<?php

namespace app\admin\controller\express;

use app\common\controller\Backend;

/**
 * 短信发送管理
 *
 * @icon fa fa-circle-o
 */
class Sms extends Backend
{
    
    /**
     * Sms模型对象
     * @var \app\admin\model\express\Sms
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\express\Sms;

    }


}
