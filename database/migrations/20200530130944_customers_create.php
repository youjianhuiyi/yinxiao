<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class CustomersCreate extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('customers',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'客户'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队id'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('name','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'姓名'])
            ->addColumn('type','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'卡类型'])
            ->addColumn('sn','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'卡号'])
            ->addColumn('sex','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'性别,0=女，1=男'])
            ->addColumn('birthday','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'出生年月日'])
            ->addColumn('address','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'地址'])
            ->addColumn('phone','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'手机号'])
            ->addColumn('phone1','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'备用手机号'])
            ->addColumn('email','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'邮箱'])
            ->addColumn('send_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'发送状态,0=未发送，1=已发送,2＝发送失败'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
