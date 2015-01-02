<?php
/*
+------------------------------------------------
|    post.php
|   =============================================
|    by nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git:https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/
error_reporting(E_ALL);
$action = (isset($_GET['action'])) ? $_GET['action'] : "null";
if ($action == "post") {
    include_once 'classes/post.class.php';
    include_once 'include/config.php';
    $check = new post();
    $results = $check->logincheck();
     
    // switch based on reg users or guest
    switch ($results) {

        case "user":

            // make a connection to the database
            include 'include/config.php';
            $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
                or die ("Couldn't connect to server.");
            $db = mysql_select_db("$database_name", $connection)
                or die("Couldn't select database.");

            // create a new post for registered users
            $cmd = new post();
            $cmd->RegUser();
            $rd = new post();
            $post_id = $_SESSION['postid'];
            $rd = new post();
            $rd->redirect();
            break;
          
        case "guest":

            // make a connection to the database
            include 'include/config.php';
            $post_id = $_SESSION['postid'];

            $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
                or die ("Couldn't connect to server.");
            $db = mysql_select_db("$database_name", $connection)
                or die("Couldn't select database.");

            // create new post & post as guest
            $cmd = new post();
            $cmd->Guest();
            $rd = new post();
            $rd->redirect();
            
             
    }


}
require_once 'classes/post.class.php';
// get the post id number
$pid = $_GET['pid'];
$_SESSION['pdel'] = $pid;
// include config file and connect to db
include 'include/config.php';
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

// new object
$post = new post();
$post->getPost($pid);

// get the vars
$id = $post->id;
$post_id = $post->post_id;
$_SESSION['post_id'] = $post_id;

$posters_name = $post->posters_name;
if ($posters_name == "guest"){
$imagesrc = "img/no.gif";	
}else {
$imagesrc = "include/avatar.php?uimage=$posters_name";		
}
$post_title = $post->post_title;
$post_syntax = $post->post_syntax;
$post_exp = $post->exp_int;
$post_text = $post->post_text;
$post_date = $post->post_date;
$post_size = $post->post_size;
$post_hits = $post->post_hits;
if ($post_hits == "") {
    $post_hits = "0";
} else {
    $post_hits = $post_hits;
}
$namelink = $post->namelink;
$bitly = $post->bitly;
// if bitly is not empty show shorten link
if (empty($bitly)) {
 $link_title = "   ";
 $bitly = null;
}
if (isset($bitly)) {
    $link_title = "&nbsp; Short Link: ";
    $bitly = "<a href='$bitly'>$bitly</a>";
}

// update view counts
$get = new post();
$get->hits();

// if the user is  a registered user update hit total count
if (isset($_SESSION['reguser']))
{
$regbool = $_SESSION['reguser'];	
}else{
  $regbool = 0;	
}
if ($regbool == "1"){
 // update the total hit count
 $get = new post();
 $get->totalHits();
// test
  $get = new post();
  $uid = $get->getuid($posters_name);
 // update the post with
 $get = new post();
 $get->updateUsrhits($uid);

}
$fdate = date('F j, Y', strtotime($post_date));
// switch on expiration
switch ($post_exp) {

    case "0":
        $expires = "never";
        break;

    case "1":
        $expires = "10 mins";
        break;
    case "2":
        $expires = "1 hour";
        break;
    case "3":
        $expires = "1 day";
        break;
    case "4":
        $expires = "1 month";
}


include_once  'templates/post.tpl.php';
?>
