<?php
/**
 * Recent posts for archive directory
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

error_reporting(0);
include('../include/config.php'); 
include('../include/db.php');
include('../include/session.php');
require_once '../classes/conn.class.php';
require_once '../classes/archive.class.php';

// Initialize connection and archive class
$conn = new Conn($mysqli, $config);
$archive = new Archive($conn->db, $config);

// Get recent posts
$recentPosts = $archive->getRecentPosts(10);
echo "<table class=\"table table-striped table-bordered table-condensed\">";

foreach ($recentPosts as $post) {
    $pasteId = $post['post_id']; 
    $title = $post['post_title']; 

    if (empty($title)) {
        $title = "Untitled Paste"; 
    }

    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    echo '<tr>
            <td><a href="../post.php?id='.$pasteId.'">'.$title.'</a></td>
          </tr>';
}
echo "</table>";
?>