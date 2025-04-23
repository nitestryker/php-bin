<?php
declare(strict_types=1);

/**
 * Recent posts display
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

require_once '../../include/config.php';
require_once '../../include/db.php';
require_once '../../classes/post.class.php';

try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$database_name", $dbusername, $dbpasswd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $post = new Post($pdo);
    $recentPosts = $post->getRecentPosts(10);
    
    foreach ($recentPosts as $post) {
        $title = htmlspecialchars($post['post_title'] ?? 'Untitled', ENT_QUOTES);
        $id = htmlspecialchars($post['post_id'] ?? '', ENT_QUOTES);
        
        echo "<div class='recent-post'>";
        echo "<a href='../../post.php?id=$id'>$title</a>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    error_log("Recent posts error: " . $e->getMessage());
    echo "<div class='alert alert-error'>Error loading recent posts</div>";
}
