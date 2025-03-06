
<?php
/**
 * config.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens, updated 2023
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */
 
// Security headers - only set if headers aren't sent yet
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
}

// Admin configuration
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
$config['app_version'] = "2.0.0"; // Updated version

$config['site_name'] = "phpbin"; // change the title to whatever your want

/* General 
-------------------------------------------------------------------*/
$config['site_index'] = "pb"; // folder that the pastebin is in on your server
$config['site_url'] = "https://" . $_SERVER['HTTP_HOST']; //the your site url (updated to https)
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['tmpl_file_basepath'] = $config['site_index'] . "/templates/";

/* Session security 
-------------------------------------------------------------------*/
$config['session_max_lifetime'] = 3600; // 1 hour in seconds

// Only set session ini settings if headers haven't been sent
if (!headers_sent()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1); // For HTTPS connections
}

########## Error-Catching ##########
# Note: changes as needed for debugging issues  
$db_last_error = 0;

# should I display site errors? 
$display_errors = 0; // Set to 0 for production

# Error logging - recommended for production
$error_logging = 1;

// Setup error handling
if ($display_errors) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

if ($error_logging) {
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-error.log');
}
?>
