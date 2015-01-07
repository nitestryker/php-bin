<?php
/**
 * index.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
*/


// start session 
session_start();
// error reporting 
error_reporting(0);
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

include_once 'templates/main.tpl.php';
?>
