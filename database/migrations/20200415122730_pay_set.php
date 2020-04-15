<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class PaySet extends Migrator
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
        $table = $this->table('pay_set',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'支付管理'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('pay_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付ID'])
            ->addColumn('pay_channel','string',['limit'=>30,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付通道'])
            ->addColumn('is_multiple','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'是否轮询，0=否，1=是'])
            ->addColumn('count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'使用次数'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();

    }
}
