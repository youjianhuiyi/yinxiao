<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SysconfigPay extends Migrator
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
//        -- ----------------------------
//-- Table structure for yin_pay_config
//                       -- 系统设置-支付设置
//    -- ----------------------------
//    DROP TABLE IF EXISTS `yin_pay_config`;
//CREATE TABLE `yin_pay_config` (
//    `id` int(10) unsigned NOT NULL AUTO_INCREMENT comment 'ID',
//  `team_id` int unsigned not null default 0 comment '团队ID',
//  `team_name` varchar(20) not null default '' comment '团队名称',
//  `pay_name` varchar(20) not null default '' comment '支付名称',
//  `pay_domain` varchar(255) not null default '' comment '支付域名',
//  `day_money` decimal(10,2) not null default 0.00 comment '今日收款',
//  `is_use` tinyint unsigned not null default 0 comment '是否启用0=否，1=是',
//  `config_json` json not null comment '支付参数配置',
//  PRIMARY KEY (`id`)
//) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 comment='支付设置';

        $table = $this->table('sysconfig_pay',['primary key'=>'id','auto_increment'=>true,'engine'=>'innodb','comment'=>'支付设置'])->addIndex('id');
        $table
            ->addColumn('team_id','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'团队ID'])
            ->addColumn('team_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'团队名称'])
            ->addColumn('pay_domain1','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'支付域名1'])
            ->addColumn('pay_domain2','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'支付域名2'])
            ->addColumn('pay_domain3','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'支付域名3'])
            ->addColumn('pay_name','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'支付名称'])
            ->addColumn('app_id','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'开发者ID'])
            ->addColumn('app_secret','string',['limit'=>128,'null'=>false,'default'=>'','comment'=>'开发者密钥'])
            ->addColumn('business_code','string',['limit'=>30,'null'=>false,'default'=>'','comment'=>'商户号'])
            ->addColumn('pay_secret','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'支付密钥'])
            ->addColumn('secure_secret','string',['limit'=>100,'null'=>false,'default'=>'','comment'=>'安全密钥'])
            ->addColumn('createtime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'增加时间'])
            ->addColumn('updatetime','integer',['limit'=>10,'signed'=>false,'null'=>false,'default'=>0,'comment'=>'更新时间'])
            ->addColumn('deletetime','integer',['limit'=>10,'signed'=>false,'null'=>true,'comment'=>'删除时间'])
            ->create();
    }
}
