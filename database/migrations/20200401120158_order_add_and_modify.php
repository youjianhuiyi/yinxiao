<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderAddAndModify extends Migrator
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
            ->addColumn('address','string',['limit'=>255,'after'=>'phone','null'=>false,'default'=>'','comment'=>'收货地址'])
            ->addColumn('pay_id','integer',['limit'=>10,'after'=>'pay_status','signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付通道ID'])
            ->addColumn('pay_url','string',['limit'=>120,'after'=>'pay_id','null'=>false,'default'=>'','comment'=>'支付域名'])
            ->addColumn('pid','integer',['limit'=>10,'after'=>'admin_name','signed'=>false,'null'=>false,'default'=>0,'comment'=>'上级领导人ID'])
            ->addColumn('goods_info','string',['limit'=>255,'after'=>'production_name','null'=>false,'default'=>'','comment'=>'商品信息'])
            ->addColumn('order_url','string',['limit'=>255,'after'=>'goods_info','null'=>false,'default'=>'','comment'=>'下单链接'])
            ->update();
    }
}
