
<?php
/**
 * download.php (download to text file)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
*/

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Initialize session
require_once 'include/session.php';

// Get the post id and validate
$postid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
if ($postid <= 0) {
    header("Location: index.php");
    exit;
}

/**
 * Download function to retrieve post and create a downloadable file
 * 
 * @param int $postid The post ID to download
 * @return void
 */
function download($postid) {
    // Include database connection
    require_once 'include/db.php';
    require_once 'include/config.php';
    
    try {
        global $mysqli;
        
        // Prepare statement to prevent SQL injection
        $stmt = $mysqli->prepare("SELECT postid, post_text, post_title FROM public_post WHERE postid = ?");
        $stmt->bind_param("i", $postid);
        $stmt->execute();
        $stmt->bind_result($post_id, $post_text, $post_title);
        
        if ($stmt->fetch()) {
            // Sanitize filename to avoid path traversal
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $post_title);
            $downloadfile = $filename . ".txt";
            
            // Set appropriate headers for download
            header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=\"$downloadfile\"");
            header("Content-Transfer-Encoding: binary");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            // Output the content
            echo $post_text;
        } else {
            echo "Post not found";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // Log error
        error_log("Download error: " . $e->getMessage());
        echo "An error occurred. Please try again later.";
    }
}

// Process download
download($postid);
?>
