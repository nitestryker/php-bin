SET SQL_MODE = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `public_post` (
  `public_postid` int NOT NULL AUTO_INCREMENT,
  `postid` varchar(255) NOT NULL,
  `posters_name` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `post_syntax` varchar(255) NOT NULL,
  `exp_int` int NOT NULL,
  `post_exp` varchar(255) NOT NULL,
  `post_text` longtext NOT NULL,
  `post_date` datetime NOT NULL,
  `post_size` varchar(255) NOT NULL,
  `post_hits` varchar(255) NOT NULL,
  `bitly` varchar(255) NOT NULL,
  `viewable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`public_postid`),
  KEY `postid_idx` (`postid`),
  KEY `post_date_idx` (`post_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;