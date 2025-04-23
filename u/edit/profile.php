
<?php
declare(strict_types=1);

/**
 * profile.php (users profile)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Start session with strict settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict'
]);

// Validate and sanitize input
$proId = filter_input(INPUT_GET, 'usr', FILTER_SANITIZE_STRING);
$verify = $_SESSION['verify'] ?? null;

if (!$proId) {
    header('Location: ../error.php');
    exit();
}

if ($verify === $proId) {
    require_once '../../include/config.php';
    require_once '../../classes/profile.class.php';
    
    try {
        // Create database connection using modern PDO
        $dsn = "mysql:host=$dbhost;dbname=$database_name;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbusername, $dbpasswd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        // Initialize profile
        $profile = new Profile($proId);
        
        // Get profile data
        $profileId = $profile->profileId;
        $username = $profile->username;
        $email = $profile->email;
        $website = $profile->website;
        $location = $profile->location;
        $avatar = $profile->avatar;
        $joinDate = date('F j, Y', strtotime($profile->jdate));
        
        if (empty($profileId)) {
            require 'error.php';
            exit();
        }
        
        require 'profile.tpl.php';
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header('Location: ../error.php');
        exit();
    }
} else {
    header("Location: ../$proId");
    exit();
}
