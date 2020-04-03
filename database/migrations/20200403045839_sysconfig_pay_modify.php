<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SysconfigPayModify extends Migrator
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
            ->renameColumn('business_code','mch_id')
            ->renameColumn('pay_secret','mch_key')
            ->renameColumn('secure_secret','mchv3_key')
            ->addColumn('token','string',['limit'=>32,'after'=>'pay_domain3','null'=>false,'default'=>'','comment'=>'公众号token'])
            ->addColumn('encodingaeskey','string',['limit'=>43,'after'=>'token','null'=>false,'default'=>'','comment'=>'消息加密'])
            ->addColumn('ssl_cer','string',['limit'=>120,'after'=>'encodingaeskey','null'=>false,'default'=>'','comment'=>'支付证书'])
            ->addColumn('ssl_key','string',['limit'=>120,'after'=>'ssl_cer','null'=>false,'default'=>'','comment'=>'支付证书密钥'])
            ->update();
    }
}
