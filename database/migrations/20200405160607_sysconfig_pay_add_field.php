<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SysconfigPayAddField extends Migrator
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
        $table = $this->table('sysconfig_pay');
        $table
            ->addColumn('pay_domain4','string',['limit'=>120,'after'=>'pay_domain3','null'=>false,'default'=>'','comment'=>'支付域名4'])
            ->addColumn('pay_domain5','string',['limit'=>120,'after'=>'pay_domain4','null'=>false,'default'=>'','comment'=>'支付域名5'])
            ->update();
    }
}
