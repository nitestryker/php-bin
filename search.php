
<?php
declare(strict_types=1);

/**
 * search.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Initialize session with security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

try {
    // Include required files
    require_once 'include/config.php';
    require_once 'classes/search.class.php';
    require_once 'include/db.php';

    // Create search handler
    $searchHandler = new Search($db, $config);
    
    // Get search parameters
    $searchQuery = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $syntax = filter_input(INPUT_GET, 'syntax', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Initialize results array
    $searchResults = [];
    
    // Perform search based on parameters
    if (!empty($searchQuery)) {
        $searchResults = $searchHandler->searchPosts($searchQuery);
    } elseif (!empty($syntax)) {
        $searchResults = $searchHandler->searchBySyntax($syntax);
    } elseif (!empty($username)) {
        $searchResults = $searchHandler->searchByUser($username);
    }
    
    // Format results for display
    $formattedResults = $searchHandler->formatResults($searchResults);
    
    // Include template with results
    require_once 'templates/search.tpl.php';

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    header('Location: include/error.php?msg=' . urlencode('Search failed. Please try again.'));
    exit;
}
