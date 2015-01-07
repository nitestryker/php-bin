<?php
/**
 * avatar.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
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
if (empty($imagebytes)) {
    $img = array();
$img[] = "http://bumbumbum.me/wp-content/uploads/2010/01/Batman_avatar-e1263852269689.jpg";
$img[] = "http://lh3.ggpht.com/_qUxAU04uRNA/TFsP0GdjefI/AAAAAAAACVA/fsijFwVJ9kI/Facebook-simpsons-1.jpg";
$img[] = "http://th05.deviantart.net/fs70/200H/f/2011/259/8/7/anonymous__by_d_4_nn_3_1-d4a24ra.jpg";
$img[] = "http://s3-ak.buzzfeed.com/static/enhanced/terminal01/2011/2/15/13/enhanced-buzz-16839-1297795475-9.jpg";
$img[] = "http://fc03.deviantart.net/fs70/f/2012/034/d/2/facebook_avatar___pikachu_by_heatphoenix-d4ojd5a.png";

rand();
$url =  $img[rand(0, sizeof($img)-1)];
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . filesize($file));
    readfile($url);
die();
} else {
    print $imagebytes;
}
?>