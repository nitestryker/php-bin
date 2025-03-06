
<?php
/**
 * Raw View Page
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

// Disable output buffering
while (ob_get_level()) {
    ob_end_clean();
}

// Initialize connection
$conn = new Conn($mysqli, $config);

// Validate the post ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid post ID';
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
        header('HTTP/1.1 403 Forbidden');
        echo 'This paste is not viewable';
        exit;
    }

    // Set the content type
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: inline; filename="paste-'.$id.'.txt"');
    
    // Output the raw code
    echo $row['post_text'];
} else {
    header('HTTP/1.1 404 Not Found');
    echo 'Post not found';
}
$stmt->close();
?>
