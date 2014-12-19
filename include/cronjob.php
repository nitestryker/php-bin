<?php
/*
+------------------------------------------------
|    cronjob.php
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
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
   
        $date = date('Y-m-d H:i:s');
        echo "Cron Job run on:  $date";
     // delete expired post in reg users bin
  echo "<Br>";
  
  // if expired post by reg user delete it from personal bin 
   if ($posters_name <> "guest"){
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