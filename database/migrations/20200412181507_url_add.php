<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UrlAdd extends Migrator
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
        $table = $this->table('production_url');
        $table
            ->addColumn('check_code','string',['limit'=>32,'after'=>'order_done','null'=>false,'default'=>'','comment'=>'推广码'])
            ->addColumn('query_string','string',['limit'=>100,'after'=>'check_code','null'=>false,'default'=>'','comment'=>'推广字串'])
            ->update();
    }
}
