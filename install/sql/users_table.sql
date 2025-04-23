
SET SQL_MODE = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `usersid` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `avatar` mediumblob,
  `join_date` date NOT NULL,
  `total_post` int NOT NULL DEFAULT '0',
  `total_hits` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`usersid`),
  UNIQUE KEY `username_idx` (`username`),
  KEY `uid_idx` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
