<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class OrderTestCreate extends Migrator
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
        $table = $this->table('order_test',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'测试商户订单'])->addIndex('id');
        $table
            ->addColumn('sn','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'订单号'])
            ->addColumn('admin_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'业务员ID'])
            ->addColumn('pid','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'上级ID'])
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('production_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'商品ID'])
            ->addColumn('production_name','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'商品名称'])
            ->addColumn('date','string',['limit'=>10,'null'=>false,'default'=>'','comment'=>'日期'])
            ->addColumn('name','string',['limit'=>50,'null'=>false,'default'=>'','comment'=>'收货人'])
            ->addColumn('phone','string',['limit'=>20,'null'=>false,'default'=>'','comment'=>'手机号'])
            ->addColumn('price','decimal',['precision'=>10,'scale'=>2,'signed'=>false,'null'=>false,'default'=>0.00,'comment'=>'商品价格'])
            ->addColumn('num','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'订单商品数量'])
            ->addColumn('address','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'收货地址'])
            ->addColumn('goods_info','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'商品信息'])
            ->addColumn('check_code','string',['limit'=>32,'null'=>false,'default'=>'','comment'=>'推广码'])
            ->addColumn('pay_type','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付类型,0=微信，1=享钱'])
            ->addColumn('pay_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付通道ID'])
            ->addColumn('openid','string',['limit'=>120,'null'=>false,'default'=>'','comment'=>'openid'])
            ->addColumn('order_ip','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'下单IP'])
            ->addColumn('notify_data','text',['null'=>false,'comment'=>'支付回调信息'])
            ->addColumn('transaction_id','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'支付平台订单号'])
            ->addColumn('xdd_trade_no','string',['limit'=>255,'null'=>false,'default'=>'','comment'=>'享钱订单'])
            ->addColumn('pay_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'支付状态'])
            ->addColumn('order_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'订单状态'])
            ->addColumn('summary_status','integer',['limit'=>MysqlAdapter::INT_TINY,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'统计状态,0=未统计，1=已统计'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
