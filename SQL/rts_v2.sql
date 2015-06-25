#CREATE DATABASE rts;
use rts;
CREATE TABLE `user_info` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` char(20) NOT NULL,
  `passwd` char(20) NOT NULL,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `uname` (`uname`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

CREATE TABLE `battle` (
  `b_id` int(11) NOT NULL AUTO_INCREMENT,
  `begin_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_time` smallint(6) DEFAULT NULL,
  `user1_destroy` smallint(6) DEFAULT NULL,
  `user1_id` int(11) DEFAULT NULL,
  `user2_id` int(11) DEFAULT NULL,
  `winner_name` char(20) DEFAULT NULL,
  `user2_destroy` smallint(6) DEFAULT NULL,
  `user1_money` int(11) DEFAULT NULL,
  `user2_money` int(11) DEFAULT NULL,
  `user1_name` char(20) DEFAULT NULL,
  `user2_name` char(20) DEFAULT NULL,
  PRIMARY KEY (`b_id`),
  KEY `user1_id` (`user1_id`),
  KEY `user2_id` (`user2_id`),
  KEY `user1_name` (`user1_name`),
  KEY `user2_name` (`user2_name`),
  KEY `winner_name` (`winner_name`),
  CONSTRAINT `battle_ibfk_1` FOREIGN KEY (`user1_name`) REFERENCES `user_info` (`uname`),
  CONSTRAINT `battle_ibfk_2` FOREIGN KEY (`user2_name`) REFERENCES `user_info` (`uname`),
  CONSTRAINT `battle_ibfk_3` FOREIGN KEY (`winner_name`) REFERENCES `user_info` (`uname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rank` (
  `u_id` int(11) NOT NULL DEFAULT '0',
  `play_count` int(11) DEFAULT NULL,
  `win_count` int(11) DEFAULT NULL,
  `u_name` char(20) DEFAULT NULL,
  `win_rate` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`u_id`),
  CONSTRAINT `rank_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `user_info` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

