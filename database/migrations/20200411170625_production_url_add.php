<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ProductionUrlAdd extends Migrator
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
            ->addColumn('admin_id','integer',['limit'=>10,'after'=>'team_name','signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('admin_name','string',['limit'=>100,'after'=>'admin_id','null'=>false,'default'=>'','comment'=>'业务员名称'])
            ->addColumn('domain_url','string',['limit'=>100,'after'=>'admin_name','null'=>false,'default'=>'','comment'=>'入口域名'])
            ->addColumn('ip_count','integer',['limit'=>10,'after'=>'count','signed'=>false,'null'=>false,'default'=>0,'comment'=>'ip次数'])
            ->update();

    }
}
