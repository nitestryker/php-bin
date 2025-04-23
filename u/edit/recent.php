<?php
declare(strict_types=1);

/**
 * Recent posts display
 * @package PHP-Bin
 * @version 2.0.0
 */

require_once '../../include/config.php';
require_once '../../include/db.php';

try {
    $query = "SELECT postid, post_title, post_date FROM public_post WHERE viewable = 1 ORDER BY post_date DESC LIMIT 10";
    $result = db_query($query);

    if (db_num_rows($result) > 0) {
        echo '<table class="table table-striped table-bordered table-condensed">';
        while ($row = db_fetch_assoc($result)) {
            $postId = htmlspecialchars($row['postid'] ?? '', ENT_QUOTES);
            $title = htmlspecialchars($row['post_title'] ?? 'Untitled', ENT_QUOTES);
            
            echo sprintf(
                '<tr><td><a href="../../post.php?id=%s">%s</a></td></tr>',
                $postId,
                $title
            );
        }
        echo '</table>';
    } else {
        echo '<p>No recent posts found.</p>';
    }
} catch (Exception $e) {
    error_log("Recent posts error: " . $e->getMessage());
    echo '<div class="alert alert-error">Error loading recent posts</div>';
}
?>
