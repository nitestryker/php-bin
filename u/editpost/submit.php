<?php
declare(strict_types=1);

/**
 * submit.php (submit updates)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

$profile_id = $_SESSION['profile_id'] ?? '';
$posters_name = $_SESSION['verify'] ?? '';
$pid = $_SESSION['pid'] ?? '';
$uid = $_SESSION['uid'] ?? '';

if ($posters_name !== $profile_id) {
    header("Location: ../u/" . htmlspecialchars($profile_id, ENT_QUOTES));
    exit();
}

require_once '../../include/config.php';
require_once '../../include/db.php';

try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$database_name", $dbusername, $dbpasswd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $post_text = $_POST['post_text'] ?? '';
    $post_syntax = $_POST['post_syntax'] ?? '';
    $post_exp = $_POST['post_exp'] ?? '';
    $post_title = $_POST['post_title'] ?? 'untitled';
    $expose = $_POST['exposure'] ?? 'public';

    $viewable = ($expose === "public") ? 1 : 0;

    // Calculate expiration date
    $post_exp = match($post_exp) {
        "1" => (new DateTime())->modify("+10 minutes")->format('Y-m-d H:i:s'),
        "2" => (new DateTime())->modify("+1 hour")->format('Y-m-d H:i:s'),
        "3" => (new DateTime())->modify("+1 day")->format('Y-m-d H:i:s'),
        "4" => (new DateTime())->modify("+1 month")->format('Y-m-d H:i:s'),
        default => null
    };

    $post_date = (new DateTime())->format('Y-m-d H:i:s');
    $post_size = number_format(strlen($post_text) / 1024);
    $users_ip = getClientIp();

    // Update personal bin
    $stmt = $pdo->prepare("UPDATE userp_? SET post_title = ?, post_syntax = ?, post_exp = ?, 
                          post_text = ?, post_size = ?, viewable = ? WHERE postid = ?");
    $stmt->execute([$uid, $post_title, $post_syntax, $post_exp, $post_text, $post_size, $viewable, $pid]);

    // Update public version if post is public
    if ($viewable) {
        $stmt = $pdo->prepare("UPDATE public_post SET post_title = ?, post_syntax = ?, post_exp = ?, 
                              post_text = ?, post_size = ?, viewable = ? WHERE postid = ?");
        $stmt->execute([$post_title, $post_syntax, $post_exp, $post_text, $post_size, $viewable, $pid]);
    }

    header("Location: ../../$pid");
    exit();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: error.php");
    exit();
}

function getClientIp(): string {
    $headers = apache_request_headers() ?? $_SERVER;

    if (isset($headers['X-Forwarded-For']) && 
        filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $headers['X-Forwarded-For'];
    }

    if (isset($headers['HTTP_X_FORWARDED_FOR']) && 
        filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $headers['HTTP_X_FORWARDED_FOR'];
    }

    return filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ?: '';
}