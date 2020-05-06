<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ConsumablesAddIsInUse extends Migrator
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
        $table = $this->table('consumables_domain');
        $table
            ->addColumn('is_inuse','integer',['limit'=>MysqlAdapter::INT_TINY,'after'=>'is_rand','signed'=>false,'null'=>false,'default'=>0,'comment'=>'是否在使用，0=未使用，1=使用中'])
            ->update();
    }
}
