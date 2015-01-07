<?php
/**
 * register.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
// get required classes
require  'classes/reg.class.php';
require  'include/config.php';

// create a new object
$new = new reg();

// show registration form
$new->showform();

// step two 
$action = (isset($_GET['action'])) ? $_GET['action'] : "null";
if ($action == "step2") {

// make a connection to the database
    include 'include/config.php';
    $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
        or die ("Couldn't connect to server.");
    $db = mysql_select_db("$database_name", $connection)
        or die("Couldn't select database.");

// start registration process
    $regme = new reg();
    $regme->regUser();
}

?>