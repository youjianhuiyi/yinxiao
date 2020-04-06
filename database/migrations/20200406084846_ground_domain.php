<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class GroundDomain extends Migrator
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
        $table = $this->table('ground_domain',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'落地域名'])->addIndex('id');
        $table
            ->addColumn('domain_url','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'域名链接'])
            ->addColumn('count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'使用次数'])
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('is_forbidden','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'是否被封,0=否,1=是'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
