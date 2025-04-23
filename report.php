
<?php
declare(strict_types=1);

/**
 * Report Page
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Include required files
require_once 'include/config.php';
require_once 'include/db.php';
require_once 'include/session.php';
require_once 'classes/conn.class.php';
require_once 'classes/main.class.php';

try {
    // Initialize connection and main class
    $conn = new Conn(db_connect(), $config);
    $main = new Main($conn);

    // Start session with security settings
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true
    ]);

    $error = null;
    $message = null;

    // Handle the report form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!hash_equals($_POST['csrf_token'] ?? '', $_SESSION['csrf_token'] ?? '')) {
            throw new RuntimeException('Invalid CSRF token');
        }
        
        // Sanitize and validate inputs
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (!$postId || !$email || !$reason) {
            throw new RuntimeException('All fields are required and must be valid');
        }

        // Send the report
        if ($main->reportPost($postId, $email, $reason)) {
            $message = "Your report has been submitted. Thank you for helping to keep our site clean.";
        } else {
            throw new RuntimeException('Failed to submit report');
        }
    }

    // Generate a new CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

} catch (Exception $e) {
    error_log("Report error: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($config['site_name']) ?> - Report Abuse</title>
    <meta name="description" content="Report inappropriate content">
    <meta name="keywords" content="pastebin, code sharing, report"/>
    <meta name="author" content="PHP-Bin">

    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="span12">
            <h1>Report Inappropriate Content</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$message): ?>
                <form method="post" action="report.php" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="post_id" value="<?= filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? '' ?>">
                    
                    <div class="control-group">
                        <label class="control-label" for="email">Your Email:</label>
                        <div class="controls">
                            <input type="email" id="email" name="email" class="input-xlarge" required 
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="reason">Reason for Report:</label>
                        <div class="controls">
                            <textarea id="reason" name="reason" class="input-xlarge" rows="5" required 
                                    maxlength="1000"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                        <a href="index.php" class="btn">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
            
            <p><a href="index.php">Return to Home</a></p>
        </div>
    </div>
</div>
</body>
</html>
