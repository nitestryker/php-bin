-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: sql202.byethost13.com
-- Generation Time: Aug 05, 2014 at 05:43 PM
-- Server version: 5.6.16-64.2-56
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `b13_15145190_phpbin`
--

-- --------------------------------------------------------

--
-- Table structure for table `public_post`
--
CREATE TABLE IF NOT EXISTS `public_post` (
  `public_postid` int(11) NOT NULL AUTO_INCREMENT,
  `postid` varchar(255) NOT NULL,
  `posters_name` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `post_syntax` varchar(255) NOT NULL,
  `exp_int` int(255) NOT NULL,
  `post_exp` varchar(255) NOT NULL,
  `post_text` text NOT NULL,
  `post_date` datetime NOT NULL,
  `post_size` varchar(255) NOT NULL,
  `post_hits` varchar(255) NOT NULL,
  `bitly` varchar(255) NOT NULL,
  `viewable` int(11) NOT NULL,
  PRIMARY KEY (`public_postid`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
