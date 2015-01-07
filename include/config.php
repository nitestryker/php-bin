<?php
/**
 * config.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
 $config['site_admin_email'] = "administrator@yoursite.com";

/*  Database variables
-------------------------------------------------------------------------------------*/
$dbhost = 'localhost';
$dbusername = 'root';
$dbpasswd = '';
$database_name = 'phpbin';

/* Bit.ly API
------------------------------------------------------------------------------------*/
$config['bitly_username'] = "";
$config['bitly_api'] = "";


/* Time Zone Stuff
------------------------------------------------------------------------------------*/
date_default_timezone_set('America/Los_Angeles');


/* Universal Variables 
-------------------------------------------------------*/
$config['app_name'] = "phpbin"; // do not edit this 
$config['app_version'] = "1.0.8 "; // do no edit this\

$config['site_name'] = "phpbin"; // change the title to whatever your want

/* General 
-------------------------------------------------------------------*/
$config['site_index'] = "pb"; // folder that the pastebin is in on your server
$config['site_url'] = "http://" . $_SERVER['HTTP_HOST']; //the your site url
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['tmpl_file_basepath'] = $config['site_index'] . "/templates/";

########## Error-Catching ##########
# Note:  changes as needed for debugging issues  
$db_last_error = 0;

# should I display site errors? 
$display_errors  = 1;

# this is to log errors to a file
$error_logging = 0;
?>