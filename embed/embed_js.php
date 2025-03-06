<?php
/**
 * Embed JS - Embeds a paste in an external site
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Include required files
require_once '../include/config.php';
require_once '../include/db.php';
require_once '../classes/conn.class.php';
require_once '../include/geshi.php';

// Set appropriate content type for JavaScript
header('Content-Type: application/javascript');

// Initialize connection
$conn = new Conn($mysqli, $config);

// Validate the post ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo 'document.write("<div style=\'color:red\'>Invalid paste ID</div>");';
    exit;
}

// Get the post data
$stmt = $conn->db->prepare("SELECT * FROM public_post WHERE post_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // Check if post is viewable
    if ($row['viewable'] != 1) {
        echo 'document.write("<div style=\'color:red\'>This paste is not viewable</div>");';
        exit;
    }

    $title = !empty($row['post_title']) ? $row['post_title'] : 'Untitled';
    $code = $row['post_text'];
    $syntax = $row['post_syntax'];

    // Set up GeSHi for syntax highlighting
    $geshi = new GeSHi($code, $syntax);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $highlighted_code = $geshi->parse_code();

    // Escape for JavaScript output
    $title = addslashes($title);
    $highlighted_code = addslashes($highlighted_code);

    // Create the embedded output
    echo <<<JAVASCRIPT
(function() {
    var html = '<div style="border:1px solid #ccc;padding:10px;margin:10px 0;background:#f9f9f9;max-width:800px;max-height:600px;overflow:auto;">';
    html += '<div style="font-family:Arial,sans-serif;margin-bottom:10px;font-weight:bold;">' + '{$title}' + '</div>';
    html += '{$highlighted_code}';
    html += '<div style="font-size:11px;margin-top:10px;text-align:right;font-family:Arial,sans-serif;">';
    html += 'Shared from <a href="{$config['site_url']}/post.php?id={$id}" target="_blank">{$config['site_name']}</a>';
    html += '</div>';
    html += '</div>';
    document.write(html);
})();
JAVASCRIPT;
} else {
    echo 'document.write("<div style=\'color:red\'>Paste not found</div>");';
}
$stmt->close();
?>