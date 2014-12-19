<?php
/*
+------------------------------------------------
|    avatar.php (gets users avatar)
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/
// include config file
include_once  'config.php';

// get the userid for the picture
$uimage = $_GET['uimage'];
// make connection to the database 
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

// SQL query 
$query = mysql_query("SELECT * FROM users WHERE username = '$uimage'") or die(mysql_error());
$row = mysql_fetch_assoc($query);
$imagebytes = $row['avatar'];
header("Content-type: image/jpeg");
if (empty($imagebytes)) {
    $query = mysql_query("SELECT * FROM users WHERE username ='devnull'") or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $imagebytes = $row['avatar'];
    header("Content-type: image/png");
    print $imagebytes;
} else {
    print $imagebytes;
}
?>