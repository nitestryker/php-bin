
<?php
/**
 * session.php - Secure Session Handling
 * 
 * @package PHP-Bin
 * @version 2.0.0
 */

// Load configuration
require_once 'config.php';

// Set session cookie parameters before session_start()
session_name('PHPBIN_SESSION');

// Set session cookie parameters for security
$secure = isset($_SERVER['HTTPS']) ? true : false; // Only send cookie over HTTPS if available
$httponly = true; // Prevent JavaScript access to session cookie
$samesite = 'Lax'; // Restrict cross-site request forgery

// PHP 7.3+ supports SameSite in session_set_cookie_params
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => $config['session_max_lifetime'],
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
} else {
    session_set_cookie_params(
        $config['session_max_lifetime'],
        '/; SameSite=' . $samesite,
        '',
        $secure,
        $httponly
    );
}

// Start the session
session_start();

// Regenerate session ID periodically to prevent fixation attacks
if (!isset($_SESSION['last_regeneration']) || 
    (time() - $_SESSION['last_regeneration']) > 1800) { // 30 minutes
    
    // Regenerate session ID and update timestamp
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Session expiration check
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > $config['session_max_lifetime'])) {
    
    // Session expired, destroy it
    session_unset();
    session_destroy();
    
    // Start a new session
    session_start();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Set Security Headers - Repeat from config for consistency
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
?>
