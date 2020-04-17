<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderAddNo extends Migrator
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
            ->addColumn('xdd_tmp_no','string',['limit'=>50,'after'=>'order_ip','null'=>false,'default'=>'','comment'=>'临时订单号'])
            ->addColumn('xdd_trade_no','string',['limit'=>50,'after'=>'xdd_tmp_no','null'=>false,'default'=>'','comment'=>'享多多订单号'])
            ->update();
    }
}
