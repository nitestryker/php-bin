
<?php
declare(strict_types=1);

/**
 * archive.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

require_once 'include/config.php';
require_once 'include/db.php';
require_once 'classes/main.class.php';
require_once 'classes/archive.class.php';

// Initialize main objects with error handling
try {
    $main = new Main($connection, $config);
    $archive = new Archive($connection, $config);

    // Get and validate page parameter
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
    $page = max(1, $page);

    // Get and sanitize filter parameter
    $filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

    // Set posts per page and initialize variables
    $postsPerPage = 20;
    $totalPosts = 0;
    $posts = [];

    // Get posts based on filter
    if (empty($filter) || $filter === 'recent') {
        $posts = $archive->getRecentPosts($postsPerPage);
        $totalPosts = $archive->getStatistics()['total_public_posts'];
    } elseif ($filter === 'popular') {
        $posts = $archive->getPopularPosts($postsPerPage);
        $totalPosts = $archive->getStatistics()['total_public_posts'];
    } else {
        $posts = $archive->getPostsBySyntax($filter, $postsPerPage);
        $totalPosts = count($posts);
    }

    // Calculate pagination
    $totalPages = max(1, ceil($totalPosts / $postsPerPage));
    $page = min($page, $totalPages);

    // Get syntax options and statistics
    $syntaxOptions = $archive->getSyntaxOptions();
    $stats = $archive->getStatistics();

    // Include template with error handling
    if (!include_once 'templates/archive.tpl.php') {
        throw new RuntimeException('Failed to load archive template');
    }

} catch (Exception $e) {
    error_log("Archive error: " . $e->getMessage());
    include_once 'templates/error.tpl.php';
}
