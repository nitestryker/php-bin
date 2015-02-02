<?php
/**
 * submit.php (submit updates)
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
$profile_id   = $_SESSION['profile_id'];
$posters_name = $_SESSION['verify'];
$pid          = $_SESSION['pid'];
$uid          = $_SESSION['uid'];
if ($posters_name != $profile_id) {
    // throw them out!!! 
    header("refresh:0; url=../u/$profile_id");
    exit();
}
$postexpo = $_POST['exposure'];
include_once '../../include/config.php';
// make connection to database
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd") or die("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection) or die("Couldn't select database.");
$post_text   = $_POST['post_text'];
$post_text   = mysql_real_escape_string($post_text);
$post_syntax = $_POST['post_syntax'];
$post_exp    = $_POST['post_exp'];
$exp_int     = $_POST['post_exp'];
$post_title  = $_POST['post_title'];
$expose      = $_POST['exposure'];
switch ($expose) {
    case "private":
        //set viewable to false;
        $viewable = "0";
        break;
    case "public":
        $viewable = "1";
        break;
}
if ($expose == "public") {
    $viewable = 1;
} else {
    $viewable = 0;
}
// if the title is blank call it untitled
if ($post_title == "") {
    $post_title = "untitled";
} else {
    $post_title = $_POST['post_title'];
}
// calculate expiration date 
switch ($post_exp) {
    // 10 mins
    case 1:
        $date = new DateTime();
        $date->modify("+10 minutes");
        $date     = $date->format('Y-m-d H:i:s');
        $post_exp = $date;
        break;
    // 1 hour 
    case 2:
        $date = new DateTime();
        $date->modify("+1 hour");
        $date     = $date->format('Y-m-d H:i:s');
        $post_exp = $date;
        break;
    // 24 hours 
    case 3:
        $date = new DateTime();
        $date->modify("+1 day");
        $date     = $date->format('Y-m-d H:i:s');
        $post_exp = $date;
        break;
    // 1 month 
    case 4:
        $date = new DateTime();
        $date->modify("+1 month");
        $date     = $date->format('Y-m-d H:i:s');
        $post_exp = $date;
        break;
}
date_default_timezone_set('America/Los_Angeles');
$post_date = date('Y-m-d H:i:s');
$post_size = serialize($post_text);
$post_size = strlen($post_text) / 1024;
$post_size = number_format($post_size);
$post_hits = null;
/*
 * if the post is private do not include in public bin
 */
// let's store the users ip address
$users_ip  = get_ip();
// update personal bin 
mysql_query("UPDATE userp_$uid SET post_title= '$post_title', post_syntax='$post_syntax', post_exp='$post_exp', post_text='$post_text', post_size='$post_size', viewable='$viewable' WHERE postid ='$pid'");
// if post is still public upate it the public version 
mysql_query("UPDATE public_post SET post_title= '$post_title', post_syntax='$post_syntax', post_exp='$post_exp', post_text='$post_text', post_size='$post_size', viewable='$viewable' WHERE postid ='$pid'");
header("refresh:0; url=../../$pid");
// get the users ip address 
function get_ip() {
    //Just get the headers if we can or else use the SERVER global
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    } else {
        $headers = $_SERVER;
    }
    //Get the forwarded IP if it exists
    if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $the_ip = $headers['X-Forwarded-For'];
    } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    } else {
        $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
    return $the_ip;
}
?>