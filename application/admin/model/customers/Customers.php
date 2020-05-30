<?php

namespace app\admin\model\customers;

use app\admin\model\team\Team;
use think\Model;
use traits\model\SoftDelete;

class Customers extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'customers';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'team_id_text'
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
    public function getTeamIdTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['team_id']) ? $data['team_id'] : '');
        $list = $this->getTeamNameText();
        return isset($list[$value]) ? $list[$value] : '';
    }







}
