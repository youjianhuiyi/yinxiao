<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ProductionUrlAddShareCode extends Migrator
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
            ->addColumn('share_url','string',['limit'=>255,'after'=>'url','null'=>false,'default'=>'','comment'=>'分享链接'])
            ->addColumn('share_code','string',['limit'=>40,'after'=>'check_code','null'=>false,'default'=>'','comment'=>'分享唯一码'])
            ->addColumn('share_count','integer',['limit'=>10,'after'=>'share_code','signed'=>false,'null'=>false,'default'=>0,'comment'=>'分享码访问次数'])
            ->addColumn('share_code_status','integer',['limit'=> MysqlAdapter::INT_TINY,'after'=>'share_count','signed'=>false,'null'=>false,'default'=>0,'comment'=>'分享码状态'])
            ->update();
    }
}
