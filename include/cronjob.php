<?php
/**
 * cronjob.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
error_reporting(1);
 include 'config.php';
$now = date('Y-m-d H:i:s');
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
     or die ("Couldn't connect to server.");
   	    $db = mysql_select_db("$database_name", $connection)
               or die("Couldn't select database.");

$sql= "SELECT * FROM public_post WHERE public_postid = public_postid";


$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
$postid = $row['postid'];
$post_exp = $row['post_exp'];
$post_date = $row['post_date'];
$posters_name = $row['posters_name'];
if ($post_exp <> 0 ){
$today = date('Y-m-d H:i:s');
$expire = $post_exp;
$today_time = strtotime($today);
$expire_time = strtotime($expire);
$pid = $postid;
if ($expire_time < $today_time) {
 include 'config.php';
            $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");
$sql = "DELETE FROM `public_post` WHERE `postid` = $postid";
$result = mysql_query($sql);
   
  // if expired post by reg user delete it from personal bin 
   if ($posters_name != "guest"){
   	// get uid from username
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
     or die ("Couldn't connect to server.");
   	    $db = mysql_select_db("$database_name", $connection)
               or die("Couldn't select database.");

$sql= "SELECT * FROM users WHERE username = $posters_name";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
$uid = $row['uid'];
echo $uid;
}
 include 'config.php';
 // Delete from Users Bin
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
     or die ("Couldn't connect to server.");
   	    $db = mysql_select_db("$database_name", $connection)
               or die("Couldn't select database.");
$sql = "DELETE FROM userp_$uid WHERE postid = $postid";
$result = mysql_query($sql);
   	
   } // end of  if statement  

      }
          }
      
}

?>