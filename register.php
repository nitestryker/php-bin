<?php
/*
+------------------------------------------------
|    register.php (account registration)
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
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