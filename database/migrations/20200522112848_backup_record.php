<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class BackupRecord extends Migrator
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
        $table = $this->table('backup_record',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'备份记录'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队id'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'操作员ID'])
            ->addColumn('ossconfig_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'OSS配置ID'])
            ->addColumn('file_name','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'文件名'])
            ->addColumn('data_count','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'数据量'])
            ->addColumn('date','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'操作日期'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'状态,0=失败，1=成功'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
