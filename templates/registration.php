<?php
declare(strict_types=1);

/**
 * Registration Template
 * @package PHP-Bin
 * @version 2.0.0
 */

require_once 'include/config.php';

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Initialize variables with type safety
$form = '';
$uname = '';
$uid = null;
$user = '';

// Handle logged in state
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $uid = $_SESSION['uid'] ?? null;
    $user = $_SESSION['username'] ?? '';
    $uname = $user;
    
    $form = sprintf(
        'Welcome <a href="u/%s">%s</a>&nbsp;<a href="logout.php">logout</a>',
        htmlspecialchars($user, ENT_QUOTES),
        htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES)
    );
} else {
    $form = <<<HTML
        <form method="post" action="login.php">
            <input type="hidden" name="login">
            <input class="span2" type="text" name="username" placeholder="User name">
            <input class="span2" type="password" name="password" placeholder="Password">
            <input type="submit" name="submit" value="Login" class="btn"/>
        </form>
        <ul class="nav pull-right">
            <li><a href="register.php">Registration</a></li>
        </ul>
HTML;
}

// Initialize database connection using modern mysqli
require_once 'classes/conn.class.php';

// Process registration if submitted
if (isset($_POST['submit'])) {
    try {
        $conn = new Conn();
        $result = $conn->register(
            $_POST['username'] ?? '',
            $_POST['password'] ?? '',
            $_POST['email'] ?? ''
        );
        
        if ($result) {
            header('Location: login.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - PHP-Bin</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="registration-form">
            <h2>Create Account</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</body>
</html>
