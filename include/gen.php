<?php
/*
+------------------------------------------------
|    gen.php (generates a table for users post)
|   =============================================
|    by nitestryker
|   (c) 2013 nswebdev.com
|   http://www.nswebdev.com
|   =============================================
|   git: https://gitlab.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/

// required files
require 'include/config.php';
$uid = $_SESSION['uid'];
// make connection and select database 
$connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
or die ("Couldn't connect to server.");

$db = mysql_select_db("$database_name", $connection)
or die("Couldn't select database.");

// mysql query to create table 

mysql_query("CREATE TABLE IF NOT EXISTS `userp_$uid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postid` varchar(255) NOT NULL,
  `posters_name` varchar(255) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `post_syntax` varchar(255) NOT NULL,
  `exp_int` int(255) NOT NULL,
  `post_exp` varchar(255) NOT NULL,
  `post_text` text NOT NULL,
  `post_date` datetime NOT NULL,
  `post_size` varchar(255) NOT NULL,
  `post_hits` varchar(255) NOT NULL,
  `viewable` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`))")
  or die(mysql_error());
?>