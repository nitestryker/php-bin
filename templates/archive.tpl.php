<?php
declare(strict_types=1);

/**
 * archive.template.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
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

// Initialize variables
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
        <input type="hidden" name="login">
        <input class="span2" type="text" name="username" placeholder="User name">
        <input class="span2" type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Login" class="btn"/>
        </form>
        <ul class="nav pull-right">
        <li><a href="register.php">Registration</a></li>
HTML;
}

require_once 'classes/conn.class.php';

// Handle login submission
if (isset($_POST['submit'])) {
    try {
        $cmd = new Conn();
        $cmd->login(
            $_POST['username'] ?? '', 
            $_POST['password'] ?? ''
        );
        $location = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Refresh: 1; url=' . htmlspecialchars($location, ENT_QUOTES));
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
    }
}
?>