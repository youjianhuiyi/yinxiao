<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class OrderAddField extends Migrator
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
            ->addColumn('transaction_id','string',['limit'=>32,'after'=>'pay_status','null'=>false,'default'=>'','comment'=>'支付平台订单号'])
            ->addColumn('openid','string',['limit'=>32,'after'=>'transaction_id','null'=>false,'default'=>'','comment'=>'顾客openid'])
            ->addColumn('nonce_str','string',['limit'=>32,'after'=>'openid','null'=>false,'default'=>'','comment'=>'回调随机字串'])
            ->update();
    }
}
