<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class OssConfigCreate extends Migrator
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
        $table = $this->table('oss_config',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'oss配置'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队id'])
            ->addColumn('name','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'OSS名称'])
            ->addColumn('access_key_id','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'app_id'])
            ->addColumn('access_key_secret','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'app_key'])
            ->addColumn('endpoint','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'访问域名'])
            ->addColumn('bucket','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'bucket'])
            ->addColumn('object','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'object'])
            ->addColumn('type','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'类型,0=全平台，1=非全平台'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'状态,0=可用，1=不可用'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
