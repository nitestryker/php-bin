
<?php
/**
 * Report Page
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Include required files
require_once 'include/config.php';
require_once 'include/db.php';
require_once 'include/session.php';
require_once 'classes/conn.class.php';
require_once 'classes/main.class.php';

// Initialize connection and main class
$conn = new Conn($mysqli, $config);
$main = new Main($conn);

// Handle the report form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    
    // Sanitize inputs
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason'], ENT_QUOTES, 'UTF-8') : '';
    
    // Basic validation
    if (empty($post_id) || empty($email) || empty($reason)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address";
    } else {
        // Send the report
        $success = $main->reportPost($post_id, $email, $reason);
        if ($success) {
            $message = "Your report has been submitted. Thank you for helping to keep our site clean.";
        } else {
            $error = "There was an error submitting your report. Please try again later.";
        }
    }
}

// Generate a new CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $config['site_name']; ?> - Report Abuse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Report inappropriate content">
    <meta name="keywords" content="pastebin, code sharing, report"/>
    <meta name="author" content="PHP-Bin">

    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="span12">
            <h1>Report Inappropriate Content</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!isset($message)): ?>
                <form method="post" action="report.php" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo isset($_GET['id']) ? intval($_GET['id']) : ''; ?>">
                    
                    <div class="control-group">
                        <label class="control-label" for="email">Your Email:</label>
                        <div class="controls">
                            <input type="email" id="email" name="email" class="input-xlarge" required>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="reason">Reason for Report:</label>
                        <div class="controls">
                            <textarea id="reason" name="reason" class="input-xlarge" rows="5" required></textarea>
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
