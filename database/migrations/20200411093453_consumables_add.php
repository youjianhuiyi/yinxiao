<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ConsumablesAdd extends Migrator
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
            ->addColumn('team_id','integer',['limit'=>10,'after'=>'domain_url','signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>100,'after'=>'team_id','null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('is_rand','integer',['limit'=>MysqlAdapter::INT_TINY,'after'=>'team_name','signed'=>false,'null'=>false,'default'=>0,'comment'=>'是否随机,0=随机，1=固定'])
            ->update();
    }
}
