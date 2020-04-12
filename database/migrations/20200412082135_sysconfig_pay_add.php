<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class SysconfigPayAdd extends Migrator
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
            ->addColumn('grant_domain_1','string',['limit'=>120,'after'=>'pay_domain5','null'=>false,'default'=>'','comment'=>'微信授权域名1'])
            ->addColumn('grant_domain_2','string',['limit'=>120,'after'=>'grant_domain_1','null'=>false,'default'=>'','comment'=>'微信授权域名2'])
            ->addColumn('grant_domain_3','string',['limit'=>120,'after'=>'grant_domain_2','null'=>false,'default'=>'','comment'=>'微信授权域名3'])
            ->addColumn('is_forbidden','integer',['limit'=>MysqlAdapter::INT_TINY,'after'=>'mchv3_key','signed'=>false,'null'=>false,'default'=>0,'comment'=>'是否被封,0=正常，1=已封'])
            ->update();
    }
}
