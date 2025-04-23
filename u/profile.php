<?php
declare(strict_types=1);

/**
 * profile.php
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
    'cookie_samesite' => 'Strict'
]);

$proid = $_GET['usr'] ?? '';
$action = $_GET['action'] ?? 'null';
$action = cleanInput($action);

// Edit profile handling
if ($action === 'edit') {
    $verify = $_SESSION['verify'] ?? '';
    if ($verify === $proid) {
        header("Location: edit/$proid");
        exit();
    }
    header("Location: $proid");
    exit();
}

// Edit post handling
if ($action === 'editpost') {
    $verify = $_SESSION['verify'] ?? '';
    $post = $_GET['postid'] ?? '';

    if ($verify === $proid) {
        header("Location: editpost/$post");
        exit();
    }
    header("Location: $proid");
    exit();
}

require_once '../include/config.php';
require_once '../classes/profile.class.php';

try {
    $db = new PDO(
        "mysql:host=$dbhost;dbname=$database_name;charset=utf8mb4",
        $dbusername,
        $dbpasswd,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $profile = new Profile($proid);
    $_SESSION['profile_id'] = $proid;

    $profileId = $profile->profileId;
    $username = $profile->username;
    $email = $profile->email;
    $website = $profile->website;
    $location = $profile->location ?: 'N/A';
    $avatar = $profile->avatar;
    $joinDate = $profile->joinDate;

    // Get total hits
    $stmt = $db->prepare("SELECT SUM(post_hits) AS total_hits FROM userp_?");
    $stmt->execute([$profileId]);
    $result = $stmt->fetch();
    $totalHits = $result['total_hits'] ?? 0;

    $joinDate = (new DateTime($joinDate))->format('F j, Y');

    if (empty($profileId)) {
        require 'error.php';
        exit();
    }

    $isOwner = ($_SESSION['verify'] ?? '') === $username;
    $editLink = $isOwner ? "<a href='$proid&action=edit'>edit profile</a>" : '';

    // Website handling
    if (!empty($website)) {
        $website = filter_var($website, FILTER_SANITIZE_URL);
        if (!str_starts_with($website, 'http')) {
            $website = "http://$website";
        }
        if (filter_var($website, FILTER_VALIDATE_URL)) {
            $website = "<a href='" . htmlspecialchars($website, ENT_QUOTES) . "'>" . htmlspecialchars($website, ENT_QUOTES) . "</a>";
        }
    } else {
        $website = 'N/A';
    }

    // Get post count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM userp_?");
    $stmt->execute([$profileId]);
    $postCount = $stmt->fetch()['count'] ?? 0;

    require '../templates/profile.tpl.php';

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    header("Location: error.php");
    exit();
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    header("Location: error.php");
    exit();
}

function cleanInput(string $input): string {
    return htmlspecialchars(
        trim($input),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
}