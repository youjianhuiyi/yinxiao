<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SmsconfigAddUrl extends Migrator
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
        $table = $this->table('sms_config');
        $table
            ->addColumn('send_url','string',['limit'=>128,'after'=>'api_url','null'=>false,'default'=>'','comment'=>'发送接口'])
            ->addColumn('report_url','string',['limit'=>128,'after'=>'send_url','null'=>false,'default'=>'','comment'=>'报表接口'])
            ->addColumn('reply_url','string',['limit'=>128,'after'=>'report_url','null'=>false,'default'=>'','comment'=>'回复地址'])
            ->addColumn('balance_url','string',['limit'=>128,'after'=>'reply_url','null'=>false,'default'=>'','comment'=>'余额地址'])
            ->update();

    }
}
