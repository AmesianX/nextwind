DROP TABLE IF EXISTS `pw_appverify_verify`;
CREATE TABLE IF NOT EXISTS `pw_appverify_verify` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�û�uid',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '��֤����',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '��֤ʱ��',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�û���֤��';

DROP TABLE IF EXISTS `pw_appverify_verify_check`;
CREATE TABLE `pw_appverify_verify_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�û�uid',
  `username` char(15) NOT NULL DEFAULT '' COMMENT '�û���',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '��֤����',
  `data` text COMMENT '��֤�������',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�ύ���ʱ��',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid_type` (`uid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�û���֤��˱�';