<?php
declare(strict_types=1);

/**
 * Embed JS - Embeds a paste in an external site
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Set security headers
header('Content-Type: application/javascript');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

try {
    require_once '../include/config.php';
    require_once '../include/db.php';
    require_once '../classes/conn.class.php';
    require_once '../include/geshi.php';

    // Initialize database connection
    $conn = new DatabaseConnection($config);
    
    // Validate and sanitize post ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id <= 0) {
        throw new InvalidArgumentException('Invalid paste ID');
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM public_post WHERE post_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || !($row = $result->fetch_assoc())) {
        throw new RuntimeException('Paste not found');
    }

    // Verify post is viewable
    if ($row['viewable'] !== 1) {
        throw new RuntimeException('This paste is not viewable');
    }

    // Process post data
    $title = $row['post_title'] ?: 'Untitled';
    $code = $row['post_text'];
    $syntax = $row['post_syntax'];

    // Initialize GeSHi for syntax highlighting
    $geshi = new GeSHi($code, $syntax);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $highlighted_code = $geshi->parse_code();

    // Escape for JavaScript output
    $title = addslashes(htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
    $highlighted_code = addslashes($highlighted_code);
    $site_url = addslashes($config['site_url']);
    $site_name = addslashes($config['site_name']);

    // Generate embedded output
    echo <<<JAVASCRIPT
(function() {
    var html = '<div style="border:1px solid #ccc;padding:10px;margin:10px 0;background:#f9f9f9;max-width:800px;max-height:600px;overflow:auto;">';
    html += '<div style="font-family:Arial,sans-serif;margin-bottom:10px;font-weight:bold;">' + '{$title}' + '</div>';
    html += '{$highlighted_code}';
    html += '<div style="font-size:11px;margin-top:10px;text-align:right;font-family:Arial,sans-serif;">';
    html += 'Shared from <a href="{$site_url}/post.php?id={$id}" target="_blank">{$site_name}</a>';
    html += '</div>';
    html += '</div>';
    document.write(html);
})();
JAVASCRIPT;

} catch (Exception $e) {
    error_log("Embed Error: " . $e->getMessage());
    echo 'document.write("<div style=\'color:red\'>Error: ' . addslashes(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')) . '</div>");';
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
