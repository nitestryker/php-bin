<?php
declare(strict_types=1);

/**
 * download.php (download to text file)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Initialize error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Initialize session
require_once 'include/session.php';

// Get and validate the post id
$postId = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT) ?: 0;
if ($postId <= 0) {
    header("Location: index.php");
    exit;
}

/**
 * Download function to retrieve post and create a downloadable file
 */
function download(int $postId): void 
{
    try {
        require_once 'include/db.php';
        require_once 'include/config.php';
        
        global $connection;
        
        $stmt = $connection->prepare("SELECT postid, post_text, post_title FROM public_post WHERE postid = ? AND viewable = 1");
        if (!$stmt) {
            throw new RuntimeException("Failed to prepare statement");
        }
        
        $stmt->bind_param("i", $postId);
        if (!$stmt->execute()) {
            throw new RuntimeException("Failed to execute query");
        }
        
        $stmt->bind_result($post_id, $post_text, $post_title);
        
        if ($stmt->fetch()) {
            // Sanitize filename
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $post_title);
            $downloadfile = $filename . ".txt";
            
            // Set security headers
            header_remove('X-Powered-By');
            header('X-Content-Type-Options: nosniff');
            header('Content-Security-Policy: default-src \'none\'');
            
            // Set download headers
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $downloadfile . '"');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output content
            echo $post_text;
        } else {
            throw new RuntimeException("Post not found or not viewable");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Download error: " . $e->getMessage());
        header("Location: error.php?msg=" . urlencode("Download failed"));
        exit;
    }
}

// Process download
download($postId);
