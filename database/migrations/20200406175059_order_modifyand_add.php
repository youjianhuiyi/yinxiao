<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class OrderModifyandAdd extends Migrator
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
        $table = $this->table('order');
        $table
            ->changeColumn('pay_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付状态,0=未支付，1=已支付'])
            ->addColumn('express_id','integer',['limit'=>10,'after'=>'pay_status','signed'=>false,'null'=>false,'default'=>0,'comment'=>'快递Id'])
            ->addColumn('express_com','string',['limit'=>32,'after'=>'express_id','null'=>false,'default'=>'','comment'=>'快递名称'])
            ->addColumn('express_no','string',['limit'=>100,'after'=>'express_com','null'=>false,'default'=>'','comment'=>'快递单号'])
            ->update();
    }
}
