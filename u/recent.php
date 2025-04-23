
<?php
declare(strict_types=1);

/**
 * Recent posts for user directory
 *
 * @package PHP-Bin 
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

require_once '../include/config.php';
require_once '../include/db.php';
require_once '../classes/conn.class.php';
require_once '../classes/post.class.php';

try {
    // Initialize connection
    $conn = new Conn();
    $post = new Post($conn->db);
    
    // Get recent posts with proper limit
    $recentPosts = $post->getRecentPosts(10);
    
    echo '<table class="table table-striped table-bordered table-condensed">';
    
    foreach ($recentPosts as $post) {
        $postId = htmlspecialchars($post['post_id'] ?? '', ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($post['post_title'] ?? 'Untitled Paste', ENT_QUOTES, 'UTF-8');
        
        echo sprintf(
            '<tr><td><a href="../post.php?id=%s">%s</a></td></tr>',
            $postId,
            $title
        );
    }
    
    echo '</table>';
    
} catch (Exception $e) {
    error_log("Recent posts error: " . $e->getMessage());
    echo '<div class="alert alert-error">Error loading recent posts</div>';
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
