<?php
declare(strict_types=1);

/**
 * Recent posts for archive directory
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0-modern
 */

// Disable error output in production (log it instead in real environments)
error_reporting(0);

// Load dependencies
require_once '../include/config.php';
require_once '../include/db.php';
require_once '../include/session.php';
require_once '../classes/conn.class.php';
require_once '../classes/archive.class.php';

try {
    // Initialize database connection and archive handler
    $conn = new Conn($mysqli, $config);
    $archive = new Archive($conn->db, $config);

    // Fetch recent posts
    $recentPosts = $archive->getRecentPosts(10);

    echo '<table class="table table-striped table-bordered table-condensed">';

    foreach ($recentPosts as $post) {
        $pasteId = htmlspecialchars($post['post_id'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($post['post_title'] ?: 'Untitled Paste', ENT_QUOTES, 'UTF-8');

        echo <<<HTML
<tr>
    <td><a href="../post.php?id={$pasteId}">{$title}</a></td>
</tr>
HTML;
    }

    echo '</table>';

} catch (Throwable $e) {
    // In production, log this instead
    error_log('Error loading recent posts: ' . $e->getMessage());
    echo '<p>Unable to load recent posts at this time.</p>';
}
