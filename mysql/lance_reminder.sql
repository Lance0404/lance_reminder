

CREATE TABLE IF NOT EXISTS `lance_remind_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_guid` bigint(20) unsigned NOT NULL,
  `item_subject` varchar(100) NOT NULL,
  `item_remark` varchar(300) NOT NULL,
  `item_status` tinyint(1) NOT NULL DEFAULT '0',
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `notify_datetime` datetime DEFAULT NULL,
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `lance_remind_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) NOT NULL,
  `password` char(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

