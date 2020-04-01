<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ProductionSelect extends Migrator
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
        $table = $this->table('production_select',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'产品文案选择'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('sales_price','decimal',['precision'=>10,'scale'=>2,'signed'=>false,'null'=>false,'default'=>0.00,'comment'=>'销售价'])
            ->addColumn('discount','decimal',['precision'=>10,'scale'=>2,'signed'=>false,'null'=>false,'default'=>0.00,'comment'=>'优惠券'])
            ->addColumn('true_price','decimal',['precision'=>10,'scale'=>2,'signed'=>false,'null'=>false,'default'=>0.00,'comment'=>'实价'])
            ->addColumn('production_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'产品ID'])
            ->addColumn('production_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'产品名称'])
            ->addColumn('phone1','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'客服电话1'])
            ->addColumn('phone2','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'客服电话2'])
            ->addColumn('special_code','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'特征码'])
            ->addColumn('tongji','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'统计代码'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
