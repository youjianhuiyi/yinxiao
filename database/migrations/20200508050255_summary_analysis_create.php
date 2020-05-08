<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SummaryAnalysisCreate extends Migrator
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
        $table = $this->table('summary_analysis',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'数据分析表'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('pid','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'上级ID'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('gid','integer',['limit'=>10,'after'=>'admin_id','signed'=>false,'null'=>false,'default'=>0,'comment'=>'商品ID'])
            ->addColumn('date','string',['limit'=>10,'after'=>'admin_id','null'=>false,'default'=>'','comment'=>'日期'])
            ->addColumn('check_code','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'推广码'])
            ->addColumn('order_sn','string',['limit'=>32,'null'=>false,'default'=>0,'comment'=>'订单号'])
            ->addColumn('type','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'数据类型，0=订单，1=订单数量，2=支付，3=支付数量'])
            ->addColumn('count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新次数'])
            ->addColumn('data','text',['null'=>false,'default'=>'','comment'=>'原始数据'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
