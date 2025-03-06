
<?php
/**
 * archive.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

require_once 'include/config.php';
require_once 'include/db.php';
require_once 'classes/main.class.php';
require_once 'classes/archive.class.php';

// Create main object
$main = new Main($connection, $config);

// Create archive object
$archive = new Archive($connection, $config);

// Get page parameter
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

// Get filter parameter
$filter = isset($_GET['filter']) ? $main->clean($_GET['filter']) : '';

// Get posts with pagination
$postsPerPage = 20;
$totalPosts = 0;
$posts = [];

if (empty($filter) || $filter === 'recent') {
    // Recent posts
    $posts = $archive->getRecentPosts($postsPerPage);
    $totalPosts = $archive->getStatistics()['total_public_posts'];
} elseif ($filter === 'popular') {
    // Popular posts
    $posts = $archive->getPopularPosts($postsPerPage);
    $totalPosts = $archive->getStatistics()['total_public_posts'];
} else {
    // Filter by syntax
    $posts = $archive->getPostsBySyntax($filter, $postsPerPage);
    
    // Count posts with this syntax
    $stmt = $connection->prepare("SELECT COUNT(*) AS count FROM public_post WHERE post_syntax = ? AND viewable = 1");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $totalPosts = $row['count'];
    }
    $stmt->close();
}

// Calculate pagination
$totalPages = ceil($totalPosts / $postsPerPage);
if ($page > $totalPages) $page = $totalPages;

// Get syntax options for filter dropdown
$syntaxOptions = $archive->getSyntaxOptions();

// Get statistics
$stats = $archive->getStatistics();

// Include template
include_once 'templates/archive.tpl.php';
?>
