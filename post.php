
<?php
/**
 * post.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens (original), Updated 2023
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 *  
 *     V2.0.0 Changes 
 *     - Modernized PHP code
 *     - Added security features
 *     - Replaced deprecated mysql functions with mysqli
 *    
*/

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include session management
require_once 'include/session.php';

// Process post action if requested
$action = (isset($_GET['action'])) ? $_GET['action'] : "null";
if ($action == "post") {
    include_once 'classes/post.class.php';
    include_once 'include/config.php';
    $check = new post();
    $results = $check->logincheck();
     
    // Switch based on reg users or guest
    switch ($results) {
        case "user":
            // Include the database connection
            include_once 'include/db.php';
            
            // Create a new post for registered users
            $cmd = new post();
            $cmd->RegUser();
            $post_id = $_SESSION['postid'];
            $rd = new post();
            $rd->redirect();
            break;
          
        case "guest":
            // Include the database connection
            include_once 'include/db.php';
            $post_id = $_SESSION['postid'];

            // Create new post & post as guest
            $cmd = new post();
            $cmd->Guest();
            $rd = new post();
            $rd->redirect();
    }
}

// Process viewing a post
require_once 'classes/post.class.php';

// Get the post id number
$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
if ($pid <= 0) {
    // Handle invalid post ID
    header("Location: index.php");
    exit;
}

$_SESSION['pdel'] = $pid;

// Include database connection
include_once 'include/db.php';

// Create post object and get post data
$post = new post();
$post->getPost($pid);

// Get post data
$id = $post->id;
$post_id = $post->post_id;
$_SESSION['post_id'] = $post_id;

$posters_name = $post->posters_name;
if ($posters_name == "guest") {
    $imagesrc = "include/avatar.php?uimage=$posters_name";
} else {
    $imagesrc = "include/avatar.php?uimage=$posters_name";
}
$post_title = $post->post_title;
$post_syntax = $post->post_syntax;
$post_exp = $post->exp_int;
$post_text = $post->post_text;
$post_date = $post->post_date;
$post_size = $post->post_size;
$post_hits = $post->post_hits ?? 0;

$namelink = $post->namelink;
$bitly = $post->bitly;

// If bitly is not empty show shortened link
if (empty($bitly)) {
    $link_title = "";
    $bitly = null;
} else {
    $link_title = "&nbsp; Short Link: ";
    $bitly = "<a href='" . htmlspecialchars($bitly, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($bitly, ENT_QUOTES, 'UTF-8') . "</a>";
}

// Update view counts
$post->hits();

// If the user is a registered user update hit total count
$regbool = $_SESSION['reguser'] ?? 0;
if ($regbool == "1") {
    // Update total hit count
    $post->totalHits();
    
    // Get user ID and update user hits
    $uid = $post->getuid($posters_name);
    $post->updateUsrhits($uid);
}

// Format the date
$fdate = date('F j, Y', strtotime($post_date));

// Set expiration text
switch ($post_exp) {
    case "0":
        $expires = "never";
        break;
    case "1":
        $expires = "10 mins";
        break;
    case "2":
        $expires = "1 hour";
        break;
    case "3":
        $expires = "1 day";
        break;
    case "4":
        $expires = "1 month";
        break;
    default:
        $expires = "unknown";
}

// Include template
include_once 'templates/post.tpl.php';
?>
