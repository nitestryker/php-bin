<?php
/**
 * editpost.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
session_start();
error_reporting(E_ALL);
$pid             = $_GET['pid'];
$_SESSION['pid'] = $pid;
$pid             = clean($pid);
$profile_id      = $_SESSION['profile_id'];
$posters_name    = $_SESSION['verify'];

/*
 *
 *
 *     check users and then show info to be updated
 *
 */

// one more security check 
if ($posters_name != $profile_id) {
    // throw them out!!! 
    header("refresh:0; url=../u/$profile_id");
}
// everything is good let's continue.
include_once '../../include/config.php';

// make connection to database
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd") or die("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection) or die("Couldn't select database.");

// get the userid for editing 
$uid    = getuid($posters_name);
$sql    = "SELECT * FROM userp_$uid WHERE postid = $pid";
$result = mysql_query($sql);
if ($result === FALSE) {
    exit(); // TODO: better error handling
}
while ($row = mysql_fetch_array($result)) {
    $post_id               = $row['postid'];
    $post_title            = $row['post_title'];
    $post_syntax           = $row['post_syntax'];
    $exp_int               = $row['exp_int'];
    $post_exp              = $row['post_exp'];
    $viewable              = $row['viewable'];
    $_Sesssion['exposure'] = $post_exp;
    $post_text             = $row['post_text'];
}
// when the post will expire 
switch ($exp_int) {
    case "0":
        $expire = "<option value='0' selected>Never</option>";
        break;
    case "1":
        $expire = "<option value='1' selected>10 Minutes</option>";
        break;
    case "2":
        $expire = "<option value='2' selected>1 Hour</option>";
        break;
    case "3":
        $expire = "<option value='3' selected>1 Day</option>";
        break;
        $expire = "<option value='4' selected>1 Month</option>";
}

// post exposure 
switch ($post_exp) {
    case "0":
        $exposure = "<option value='public' selected>Public</option>";
        break;
    case "1":
        $exposure = "<option value='public' selected>Private</option>";
        break;
    case "2":
        $exposure = "<option value='unlisted' selected>Unlisted</option>";
        break;
}


function getuid($username = null) {
    $username = $username;
    $sql      = "SELECT * FROM users WHERE username ='$username '";
    $result   = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
        $uid             = $row['uid'];
        $_SESSION['uid'] = $uid;
        return $uid;
    }
} // end of function


function clean($var = null) {
    // sanitation
    $var = htmlspecialchars($var);
    $var = trim(htmlspecialchars($var, ENT_QUOTES, "utf-8"));
    $var = strip_tags($var);
    return $var;
}

// include the template
include 'main.tpl.php';
?>