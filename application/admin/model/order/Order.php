<?php

namespace app\admin\model\order;

use app\admin\model\Admin;
use app\admin\model\team\Team;
use think\Model;
use traits\model\SoftDelete;

class Order extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pid_text',
        'team_name_text',
//        'admin_name_text',
//        'pay_id_text',
//        'production_name_text',
//        'express_com'
    ];



    /**
     * 获取团队名称
     * @return array|bool|string
     */
    public function getTeamNameText()
    {
        return  Team::column('name','id');
    }

    /**
     * 返回团队名称
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getTeamNameTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['team_id']) ? $data['team_id'] : '');
        $list = $this->getTeamNameText();
        return isset($list[$value]) ? $list[$value] : '';
    }


    /**
     * 获取上级
     * @return array|bool|string
     */
    public function getPidText()
    {
        return  Admin::column('nickname','id');
    }

    /**
     * 返回上级数据
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getPidTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pid']) ? $data['pid'] : '');
        $list = $this->getPidText();
        return isset($list[$value]) ? $list[$value] : '';
    }

}
