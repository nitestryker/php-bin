<?php
/*
+------------------------------------------------
|    profile.php (users profile)
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/
error_reporting(0);
session_start();

$proid = $_GET['usr'];
  // verify the user
  $proid = $_GET['usr'];
  if (isset($_SESSION['verify'])){
  $verify = $_SESSION['verify'];
  }else {
  $verify = "null";
  }
    if ($verify === $proid) {
    // include need files
include_once '../../include/config.php';
include '../../classes/profile.class.php';
// make connection to database
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

// new instance
$profile = new profile($proid);

// variables from class
$profieid = $profile->profileid;
$r2 = $profile->profileid;
$username = $profile->username;
$email = $profile->email;
$website = $profile->website;
$location = $profile->location;
$avatar = $profile->avatar;
$jdate = $profile->jdate;

 $back = "<a href='../$proid'>go back</a>";
// convert join date
$join_date = date('F j, Y', strtotime($jdate));

// if userid is not found do this
if (empty($profieid)) {
    include 'error.php';
    exit();
} 

        include 'profile.tpl.php';
    }else {

        // if not verified redirect
        header("refresh:0; url=../$proid");
    }


?>