<?php
/*
+------------------------------------------------
|    config.php
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/
 $config['site_admin_email'] = "";

/*  Database variables
-------------------------------------------------------------------------------------*/
$dbhost = 'localhost';
$dbusername = 'dbuser';
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
$config['app_version'] = "1.0.4 "; // do no edit this\

$config['site_name'] = "My Site Name Here"; // change the title to whatever your want

/* General 
-------------------------------------------------------------------*/
$config['site_index'] = "pbclone"; // folder that the pastebin is in on your server
$config['site_url'] = "http://" . $_SERVER['HTTP_HOST']; //the your site url
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['tmpl_file_basepath'] = $config['site_index'] . "/templates/";
?>