SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

USE `mypie`;

CREATE TABLE IF NOT EXISTS `session` (
  `session_id` char(64) NOT NULL,
  `data` text,
  `user_agent` char(64) DEFAULT NULL,
  `ip_address` varchar(46) DEFAULT NULL,
  `time_updated` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_uq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `url` varchar(500) NOT NULL,
  `url_locked` enum('N','Y') NOT NULL DEFAULT 'N',
  `meta_description` varchar(320) DEFAULT NULL,
  `template` varchar(60) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_uq` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pagelet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `content` text,
  `content_html` text,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`),
  UNIQUE KEY `page_id_name_uq` (`page_id`, `name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pagelet`
ADD CONSTRAINT `pagelet_page_id_fk` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
