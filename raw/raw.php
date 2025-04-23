<?php
declare(strict_types=1);

/**
 * Raw View Page
 *
 * @package PHP-Bin
 * @version 2.1.0-modern
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Jeremy Stevens
 */

// Include required files
require_once '../include/config.php';
require_once '../include/db.php';
require_once '../classes/conn.class.php';

// Clear all output buffering layers
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Initialize connection
$conn = new Conn($mysqli, $config);

// Validate and sanitize the post ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    echo 'Invalid post ID';
    exit;
}

// Get the post data securely
$stmt = $conn->db->prepare("SELECT post_text, viewable FROM public_post WHERE post_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // Ensure the paste is viewable
    if ((int)$row['viewable'] !== 1) {
        http_response_code(403);
        echo 'This paste is not viewable';
        exit;
    }

    // Send plain text response headers
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: inline; filename="paste-' . $id . '.txt"');

    // Output the raw post text
    echo $row['post_text'];
} else {
    http_response_code(404);
    echo 'Post not found';
}

$stmt->close();
