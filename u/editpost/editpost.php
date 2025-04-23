<?php
declare(strict_types=1);

/**
 * editpost.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

$pid = filter_input(INPUT_GET, 'pid', FILTER_SANITIZE_NUMBER_INT);
$_SESSION['pid'] = $pid;

$profile_id = $_SESSION['profile_id'] ?? '';
$posters_name = $_SESSION['verify'] ?? '';

// Security check
if ($posters_name !== $profile_id) {
    header("Location: ../u/" . htmlspecialchars($profile_id, ENT_QUOTES));
    exit();
}

require_once '../../include/config.php';
require_once '../../include/db.php';

try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$database_name", $dbusername, $dbpasswd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $uid = getUid($posters_name, $pdo);

    $stmt = $pdo->prepare("SELECT * FROM userp_? WHERE postid = ?");
    $stmt->execute([$uid, $pid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $post_id = $row['postid'];
        $post_title = $row['post_title'];
        $post_syntax = $row['post_syntax'];
        $exp_int = $row['exp_int'];
        $post_exp = $row['post_exp'];
        $_SESSION['exposure'] = $post_exp;
        $post_text = $row['post_text'];
        $viewable = $row['viewable'];
    }

    // Set expiration options
    $expire = match($exp_int) {
        "0" => "<option value='0' selected>Never</option>",
        "1" => "<option value='1' selected>10 Minutes</option>",
        "2" => "<option value='2' selected>1 Hour</option>",
        "3" => "<option value='3' selected>1 Day</option>",
        "4" => "<option value='4' selected>1 Month</option>",
        default => "<option value='0' selected>Never</option>"
    };

    // Set exposure options
    $exposure = match($post_exp) {
        "0" => "<option value='public' selected>Public</option>",
        "1" => "<option value='private' selected>Private</option>",
        "2" => "<option value='unlisted' selected>Unlisted</option>",
        default => "<option value='public' selected>Public</option>"
    };

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: error.php");
    exit();
}

function getUid(string $username, PDO $pdo): int {
    $stmt = $pdo->prepare("SELECT uid FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['uid'] = $result['uid'];
        return (int)$result['uid'];
    }
    return 0;
}

// Include the template
require_once 'main.tpl.php';

?>