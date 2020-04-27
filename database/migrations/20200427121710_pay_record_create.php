<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PayRecordCreate extends Migrator
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
        $table = $this->table('pay_record',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'商户收款记录'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('date','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'日期'])
            ->addColumn('pay_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'商户ID'])
            ->addColumn('pay_type','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'商户类型'])
            ->addColumn('check_code','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'推广码'])
            ->addColumn('use_count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'使用次数'])
            ->addColumn('pay_nums','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付成功次数'])
            ->addColumn('money','decimal',['precision'=>12,'scale'=>2,'signed'=>false,'null'=>false,'default'=>0.00,'comment'=>'收入金额'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();

    }
}
