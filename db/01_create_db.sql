CREATE DATABASE IF NOT EXISTS `sg_problem_2`;
USE `sg_problem_2`;

DROP TABLE IF EXISTS `tblplayerdata`;
CREATE TABLE `tblplayerdata` (
  `player_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `credits` int(11) unsigned DEFAULT '0',
  `lifetime_spins` int(11) unsigned DEFAULT '0',
  `salt_val` varchar(45) NOT NULL,
  `ts_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
