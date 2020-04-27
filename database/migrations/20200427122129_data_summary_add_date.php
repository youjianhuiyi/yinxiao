<?php

use think\migration\Migrator;
use think\migration\db\Column;

class DataSummaryAddDate extends Migrator
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
        $table = $this->table('data_summary');
        $table
            ->addColumn('gid','integer',['limit'=>10,'after'=>'admin_id','signed'=>false,'null'=>false,'default'=>0,'comment'=>'商品ID'])
            ->addColumn('date','string',['limit'=>10,'after'=>'admin_id','null'=>false,'default'=>'','comment'=>'日期'])
            ->changeColumn('check_code','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'推广码'])
            ->update();
    }
}
