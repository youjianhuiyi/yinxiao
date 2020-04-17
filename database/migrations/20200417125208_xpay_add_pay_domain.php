<?php

use think\migration\Migrator;
use think\migration\db\Column;

class XpayAddPayDomain extends Migrator
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
        $table = $this->table('xpay');
        $table
            ->addColumn('openid_url','string',['limit'=>100,'after'=>'mch_key','null'=>false,'default'=>'','comment'=>'获取openid接口x'])
            ->addColumn('pay_domain_1','string',['limit'=>100,'after'=>'api_url','null'=>false,'default'=>'','comment'=>'支付域名1'])
            ->update();
    }
}
