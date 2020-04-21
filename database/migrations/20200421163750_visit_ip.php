<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class VisitIp extends Migrator
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
        $table = $this->table('visit_ip',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'访问记录'])->addIndex('id');
        $table
            ->addColumn('production_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'商品ID'])
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('url','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'推广链接'])
            ->addColumn('check_code','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'推广码'])
            ->addColumn('count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'访问次数'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
