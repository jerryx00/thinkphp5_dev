
DROP TABLE IF EXISTS `qw_hlylog`;

CREATE TABLE `qw_hlylog` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `mobile` varchar(50) DEFAULT NULL COMMENT '业务号码',
  `request` text COMMENT '请求内容',
  `respcode` varchar(20) DEFAULT NULL COMMENT '响应编码',
  `response` text COMMENT '响应内容',
  `otype` tinyint(1) DEFAULT '1' COMMENT '操作类型',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '修改时间',
  `remark` varchar(100) DEFAULT NULL COMMENT '号码说明',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `qw_hlyband`   
  ADD COLUMN `uid` INT(10) NULL COMMENT '商户ID' AFTER `id`;
  
  ALTER TABLE `qw_hlybandpre`   
  ADD COLUMN `uid` INT(10) NULL COMMENT '商户ID' AFTER `id`;
  
  ALTER TABLE `qw_hlyorder`   
  ADD COLUMN `uid` INT(10) NULL COMMENT '商户ID' AFTER `id`;
