<?php
declare(strict_types=1);

/**
 * post.php
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
require_once 'include/session.php';
require_once 'include/config.php';
require_once 'classes/post.class.php';

try {
    // Process post action if requested
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'null';

    if ($action === 'post') {
        $postHandler = new Post();
        $userType = $postHandler->loginCheck();

        switch ($userType) {
            case 'user':
                // Create a new post for registered users
                $postId = $postHandler->regUserPost();
                if ($postId === false) {
                    throw new RuntimeException('Failed to create user post');
                }
                $_SESSION['postid'] = $postId;
                $postHandler->redirect();
                break;

            case 'guest':
                // Create new post for guest
                $postId = $postHandler->guestPost();
                if ($postId === false) {
                    throw new RuntimeException('Failed to create guest post');
                }
                $_SESSION['postid'] = $postId;
                $postHandler->redirect();
                break;

            default:
                throw new RuntimeException('Invalid user type');
        }
    }

    // Process viewing a post
    $pid = filter_input(INPUT_GET, 'pid', FILTER_SANITIZE_NUMBER_INT);
    if (!$pid) {
        header('Location: index.php', true, 303);
        exit;
    }

    $_SESSION['pdel'] = $pid;

    // Create post object and get post data
    $postHandler = new Post();
    $postData = $postHandler->getPost($pid);

    if (!$postData) {
        throw new RuntimeException('Post not found');
    }

    // Extract post data
    $post_id = $postData['postid'];
    $_SESSION['post_id'] = $post_id;

    $posters_name = $postData['posters_name'];
    $imagesrc = "include/avatar.php?uimage=" . urlencode($posters_name);

    $post_title = htmlspecialchars($postData['post_title'], ENT_QUOTES, 'UTF-8');
    $post_syntax = htmlspecialchars($postData['post_syntax'], ENT_QUOTES, 'UTF-8');
    $post_exp = $postData['exp_int'];
    $post_text = $postData['post_text'];
    $post_date = $postData['post_date'];
    $post_size = $postData['post_size'];
    $post_hits = $postData['post_hits'] ?? 0;
    $bitly = $postData['bitly'];

    // Format bitly link
    $link_title = empty($bitly) ? "" : "&nbsp; Short Link: ";
    $bitly = empty($bitly) ? null : "<a href='" . htmlspecialchars($bitly, ENT_QUOTES, 'UTF-8') . "'>" . 
            htmlspecialchars($bitly, ENT_QUOTES, 'UTF-8') . "</a>";

    // Update view counts
    if (isset($_SESSION['reguser']) && $_SESSION['reguser'] === "1") {
        // Update total hit count for registered users
        $uid = $postHandler->getuid($posters_name);
        if ($uid) {
            $postHandler->updateUsrhits($uid);
        }
    }

    // Format the date
    $fdate = date('F j, Y', strtotime($post_date));

    // Set expiration text
    $expires = match($post_exp) {
        "0" => "never",
        "1" => "10 mins",
        "2" => "1 hour",
        "3" => "1 day",
        "4" => "1 month",
        default => "unknown"
    };

    // Include template
    require_once 'templates/post.tpl.php';

} catch (Exception $e) {
    error_log("Post error: " . $e->getMessage());
    header('Location: include/error.php?msg=' . urlencode($e->getMessage()));
    exit;
}

?>