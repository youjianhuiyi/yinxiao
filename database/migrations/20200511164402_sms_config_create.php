~<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class SmsConfigCreate extends Migrator
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
        $table = $this->table('sms_config',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'短信配置'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('username','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'账号'])
            ->addColumn('password','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'密码'])
            ->addColumn('app_uid','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'用户UID'])
            ->addColumn('app_key','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'用户密钥'])
            ->addColumn('code','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'接入码'])
            ->addColumn('server_id','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'服务ID'])
            ->addColumn('port','string',['limit'=>5,'null'=>false,'default'=>'','comment'=>'端口号'])
            ->addColumn('ip','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'接口IP'])
            ->addColumn('api_url','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'接口地址'])
            ->addColumn('template_1','text',['null'=>false,'comment'=>'短信模板1'])
            ->addColumn('template_2','text',['null'=>false,'comment'=>'短信模板2'])
            ->addColumn('template_3','text',['null'=>false,'comment'=>'短信模板3'])
            ->addColumn('template_4','text',['null'=>false,'comment'=>'短信模板4'])
            ->addColumn('template_5','text',['null'=>false,'comment'=>'短信模板5'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'状态'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
