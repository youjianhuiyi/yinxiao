<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class DataSummaryCreate extends Migrator
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
        $table = $this->table('data_summary',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'数据汇总'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('pid','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'上级ID'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('check_code','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'推广码'])
            ->addColumn('visit_nums','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'浏览数'])
            ->addColumn('order_count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'订单数量'])
            ->addColumn('order_nums','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'订单商品数量'])
            ->addColumn('pay_done','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付成功订单'])
            ->addColumn('pay_done_nums','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付成功商品数量'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
