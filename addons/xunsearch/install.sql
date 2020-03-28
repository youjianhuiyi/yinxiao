CREATE TABLE IF NOT EXISTS `__PREFIX__xunsearch_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) DEFAULT 0 COMMENT '项目ID',
  `name` varchar(50) DEFAULT '' COMMENT '字段名称',
  `title` varchar(100) DEFAULT '' COMMENT '字段标题',
  `type` enum('string','numeric','date','id','title','body') DEFAULT NULL COMMENT '类型',
  `index` enum('none','self','mixed','both') DEFAULT NULL COMMENT '索引方式',
  `tokenizer` varchar(100) DEFAULT NULL COMMENT '分词器',
  `cutlen` int(10) unsigned DEFAULT '0' COMMENT '摘要结果截取长度',
  `weight` int(10) unsigned DEFAULT '1' COMMENT '混合区检索时的概率权重',
  `phrase` enum('yes','no') DEFAULT 'no' COMMENT '是否支持精确检索',
  `non_bool` enum('yes','no') DEFAULT 'no' COMMENT '强制指定是否为布尔索引',
  `extra` tinyint(1) unsigned DEFAULT '1' COMMENT '是否附属字段',
  `sortable` tinyint(1) DEFAULT '1' COMMENT '是否允许排序',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Xunsearch字段列表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xunsearch_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '' COMMENT '项目名称',
  `title` varchar(100) DEFAULT '' COMMENT '项目标题',
  `charset` varchar(50) DEFAULT '' COMMENT '编码',
  `serverindex` varchar(100) DEFAULT NULL COMMENT '索引服务端',
  `serversearch` varchar(100) DEFAULT NULL COMMENT '搜索服务端',
  `logo` varchar(100) DEFAULT '' COMMENT 'Logo',
  `indextpl` varchar(100) DEFAULT '' COMMENT '搜索页模板',
  `listtpl` varchar(100) DEFAULT '' COMMENT '列表页模板',
  `isaddon` tinyint(1) unsigned DEFAULT '1' COMMENT '是否插件',
  `isfuzzy` tinyint(1) unsigned DEFAULT '1' COMMENT '是否模糊搜索',
  `issynonyms` tinyint(1) unsigned DEFAULT '1' COMMENT '是否同义词搜索',
  `isfrontend` tinyint(1) unsigned DEFAULT '1' COMMENT '是否开启前台搜索',
  `isindexhotwords` tinyint(1) unsigned DEFAULT '1' COMMENT '是否首页热门搜索',
  `ishotwords` tinyint(1) unsigned DEFAULT '0' COMMENT '是否列表页热门搜索',
  `isrelatedwords` tinyint(1) unsigned DEFAULT '1' COMMENT '是否列表页相关搜索',
  `pagesize` int(10) unsigned DEFAULT '10' COMMENT '搜索分页大小',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Xunsearch配置表';