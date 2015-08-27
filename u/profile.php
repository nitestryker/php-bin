<?php
/**
 * profile.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @author Nitestryker 
 * @copyright 2014-2015 Jeremy Stevens 
 * @copyright 2015 Nitestryker 
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
*/
error_reporting(0);
// start session
session_start();

$proid = $_GET['usr'];

// edit profile
    $action = (isset($_GET['action'])) ? $_GET['action'] : "null";
   // TODO add code to prevent SQL injection.
   $action = clean($action); 

    // verify user and then allow to edit profile      
   if ($action == "edit") {

    // verify that the logged in user is the same as the profile user
    $verify = $_SESSION['verify'];
    if ($verify == $proid) {
    header("refresh:0; url=edit/$proid");
    }else {
        // if not verified redirect
        header("refresh:0; url=$proid");
      exit();
    }
}
  // action edit paste
  if($action == "editpost") {
  $verify = $_SESSION['verify'];
   $post = $_GET['postid'];

 // verify user is who they say they are 
  if ($verify == $proid) {
    header("refresh:0; url=editpost/$post");
    }else {
        // if not verified redirect
        header("refresh:0; url=$proid");
      exit();
    }

}


// include need files
include_once '../include/config.php';
include '../classes/profile.class.php';
// make connection to database
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

// new instance
$profile = new profile($proid);
$_SESSION['profile_id'] = $proid;
// variables from class
$profieid = $profile->profileid;
$r2 = $profile->profileid;
$username = $profile->username;
$email = $profile->email;
$website = $profile->website;
$location = $profile->location;
$avatar = $profile->avatar;
$jdate = $profile->jdate;

// get the total hit count for the user
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
or die("Couldn't select database.");
$result = mysql_query("SELECT SUM(post_hits) AS value_sum FROM userp_$profieid", $connection);
$row = mysql_fetch_assoc($result);
$sum = $row['value_sum'];
$thits = $sum;

// convert join date
$join_date = date('F j, Y', strtotime($jdate));

// if userid is not found do this
if (empty($profieid)) {
    include 'error.php';
    exit();
}
// dev test
$verify = $_SESSION['verify'];
if ($verify == $username) {
    $dev = true;
} else
    $dev = false;
if ($dev == 1) {
    $edit = "<a href='$proid&action=edit'>edit profile</a>";
}
// if location is null show N/A
if ($location == "") {
    $location = "N/A";
} else {
    $location = $location;
}
// if website is blank show N/A
if ($website == "") {
    $website = "N/A";
} else {

// vaidate the url address
if (!filter_var($website, FILTER_VALIDATE_URL) === false) {
    $address = $website;
} else {
    $address = "http://$website";
}

  // remove illegal characters from address 
 $address = filter_var($address, FILTER_SANITIZE_URL);

    $website = "<a href='$address'>$address</a>";
}

// if  total hit count is null then display 0
if (empty($thits)) {
    $thits = "0";
} else {
    $thits = $thits;
}

 function clean($var = null)
    {
        // sanitation
        $var = htmlspecialchars($var);
        $var = trim(htmlspecialchars($var, ENT_QUOTES, "utf-8"));
        $var = strip_tags($var);
        return $var;
    }

//test 2
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

$result = mysql_query("SELECT * FROM userp_$profieid", $connection);
$num_rows = mysql_num_rows($result);
include '../templates/profile.tpl.php';
?>