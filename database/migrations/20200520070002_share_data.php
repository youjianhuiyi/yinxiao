<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class ShareData extends Migrator
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
        $table = $this->table('share_data',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'分享有礼'])->addIndex('id');
        $table
            ->addColumn('sn','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'分享编号'])
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队id'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('pid','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'上级ID'])
            ->addColumn('production_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'商品ID'])
            ->addColumn('production_name','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'商品名称'])
            ->addColumn('date','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'日期'])
            ->addColumn('name','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'收货人'])
            ->addColumn('phone','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'手机号'])
            ->addColumn('address','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'收货地址'])
            ->addColumn('goods_info','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'礼品信息'])
            ->addColumn('share_code','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'分享码'])
            ->addColumn('order_ip','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'下单IP'])
            ->addColumn('send_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'短信发送状态,0=未发送，1=已发送,2＝发送失败'])
            ->addColumn('comment','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'备注信息'])
            ->addColumn('summary_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'统计状态,0=未统计，1=已统计'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
