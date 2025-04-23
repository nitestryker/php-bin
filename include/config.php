<?php
declare(strict_types=1);

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\';');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Admin configuration
$config['site_admin_email'] = "administrator@yoursite.com";

// Database configuration
$dbhost = 'localhost';
$dbusername = 'root';
$dbpasswd = '';
$database_name = 'phpbin';

// Bit.ly API configuration
$config['bitly_username'] = "";
$config['bitly_api'] = "";

// Timezone configuration
date_default_timezone_set('America/Los_Angeles');

// Application configuration
$config['app_name'] = "phpbin";
$config['app_version'] = "2.0.0";
$config['site_name'] = "phpbin";
$config['site_index'] = "pb";
$config['site_url'] = "https://" . $_SERVER['HTTP_HOST'];
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['tmpl_file_basepath'] = $config['site_index'] . "/templates/";

// Session security configuration
$config['session_max_lifetime'] = 3600;

if (!headers_sent()) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', (string)$config['session_max_lifetime']);
}

// Error handling configuration
$db_last_error = 0;
$display_errors = 0;
$error_logging = 1;

if ($display_errors) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

if ($error_logging) {
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/php-error.log');
}

?>