
<?php
declare(strict_types=1);

/**
 * register.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Initialize session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Required files
require_once 'include/config.php';
require_once 'classes/reg.class.php';
require_once 'include/db.php';

try {
    // Create registration handler
    $registration = new reg();
    
    // Process registration step if action is specified
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'null';

    if ($action === 'step2') {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new RuntimeException('Invalid CSRF token');
        }

        // Process registration
        if (!$registration->regUser()) {
            throw new RuntimeException('Registration failed');
        }
    } else {
        // Generate CSRF token for the form
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Show registration form
        $registration->showform();
    }
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    header('Location: include/error.php?msg=' . urlencode($e->getMessage()));
    exit;
}
?>
