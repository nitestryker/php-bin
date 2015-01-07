<?php
/**
 * submit.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
error_reporting(0);
include_once '../../include/config.php';
session_start();
// get the name of the users profile
$proid = $_GET['usr'];

// the verified user
$user = $_SESSION['verify'];

// if a avatar is selected update avatar otherwize only update other info
if(is_uploaded_file($_FILES['q2_uploadAvatar']['tmp_name'])){
 
	$maxsize=$_POST['MAX_FILE_SIZE'];		
	$size=$_FILES['q2_uploadAvatar']['size'];
    // getting the image info..
   $imgdetails = getimagesize($_FILES['q2_uploadAvatar']['tmp_name']);
	$mime_type = $imgdetails['mime']; 
  $filename=$_FILES['q2_uploadAvatar']['name'];
  $imgData =addslashes (file_get_contents($_FILES['q2_uploadAvatar']['tmp_name'])); 

  // connect to database
   MySQL_connect("$dbhost","$dbusername","$dbpasswd");
mysql_select_db($database_name) or die("Could not select database")
    or die ("Couldn't connect to server.");
  $web = $_POST['q4_website'];

  // clean input
  $web = clean($web);
  $loc = $_POST['q3_location'];
  // clean input
  $loc = clean($loc);

  // mysql query
  mysql_query("UPDATE users SET website= '$web', location='$loc', avatar='$imgData' WHERE username ='$user'"); 
  header("refresh:0; url=../$user"); 
  // if an avatar is not uploaded then do not include it in the query because it will override the one in the database
  } else {
  
   $web = $_POST['q4_website'];
   // clean input
   $web = clean($web);

  $loc = $_POST['q3_location'];
  // clean input
  $loc = clean($loc);
  // connect to database

 MySQL_connect("$dbhost","$dbusername","$dbpasswd");
mysql_select_db($database_name) or die("Could not select database")
    or die ("Couldn't connect to server.");
  // query
  mysql_query("UPDATE users SET website= '$web', location='$loc' WHERE username ='$user'");

// redirect when complete
 header("refresh:0; url=../$user");
 }


// clean users input
function clean($var){
    $var = htmlspecialchars($var);
    $var = mysql_real_escape_string($var);
    $var = strip_tags($var);
    return $var;
    }
?>