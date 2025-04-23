<?php
declare(strict_types=1);

/**
 * submit.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

require_once '../../include/config.php';

// Start session with strict settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict'
]);

// Get verified user
$user = $_SESSION['verify'] ?? null;
if (!$user) {
    header('Location: ../error.php');
    exit();
}

try {
    // Initialize database connection
    $dsn = "mysql:host=$dbhost;dbname=$database_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbusername, $dbpasswd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // Handle avatar upload
    if (isset($_FILES['q2_uploadAvatar']) && is_uploaded_file($_FILES['q2_uploadAvatar']['tmp_name'])) {
        $maxSize = (int)$_POST['MAX_FILE_SIZE'];
        $size = $_FILES['q2_uploadAvatar']['size'];
        
        if ($size > $maxSize) {
            throw new RuntimeException('File too large');
        }
        
        // Validate image
        $imgDetails = getimagesize($_FILES['q2_uploadAvatar']['tmp_name']);
        if ($imgDetails === false) {
            throw new RuntimeException('Invalid image file');
        }
        
        $imgData = file_get_contents($_FILES['q2_uploadAvatar']['tmp_name']);
        
        // Update profile with image
        $stmt = $pdo->prepare("UPDATE users SET website = ?, location = ?, avatar = ? WHERE username = ?");
        $stmt->execute([
            filter_input(INPUT_POST, 'q4_website', FILTER_SANITIZE_URL),
            filter_input(INPUT_POST, 'q3_location', FILTER_SANITIZE_STRING),
            $imgData,
            $user
        ]);
    } else {
        // Update profile without image
        $stmt = $pdo->prepare("UPDATE users SET website = ?, location = ? WHERE username = ?");
        $stmt->execute([
            filter_input(INPUT_POST, 'q4_website', FILTER_SANITIZE_URL),
            filter_input(INPUT_POST, 'q3_location', FILTER_SANITIZE_STRING),
            $user
        ]);
    }
    
    header("Location: ../$user");
    exit();
    
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    header('Location: ../error.php');
    exit();
}
