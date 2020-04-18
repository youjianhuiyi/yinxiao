<?php

use think\migration\Migrator;
use think\migration\db\Column;

class RyPayCrate extends Migrator
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
        $table = $this->table('ry_pay',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'如意付管理'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('pay_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'支付名称'])
            ->addColumn('app_id','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'应用id'])
            ->addColumn('pay_channel','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'支付产品ID'])
            ->addColumn('mch_code','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'商户号'])
            ->addColumn('mch_key','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'商户密钥'])
            ->addColumn('api_url','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'接口地址'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
