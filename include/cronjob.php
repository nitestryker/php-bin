
<?php
declare(strict_types=1);

/**
 * Cron Job Handler
 *
 * @package PHP-Bin
 * @version 2.1.0
 */

require_once 'config.php';
require_once 'db.php';

try {
    $db = Database::getConnection();
    
    // Delete expired posts
    $stmt = $db->prepare(
        "DELETE FROM public_post 
         WHERE post_exp != '0000-00-00 00:00:00' 
         AND post_exp < NOW()"
    );
    $stmt->execute();
    
    $deletedCount = $stmt->affected_rows;
    
    // Log the cleanup
    error_log("Cron cleanup: Removed $deletedCount expired posts");
    
    // Optimize tables periodically
    $db->query("OPTIMIZE TABLE public_post");
    $db->query("OPTIMIZE TABLE users");
    
    echo "Cron job completed successfully\n";
    
} catch (Exception $e) {
    error_log("Cron job error: " . $e->getMessage());
    echo "Cron job failed: " . $e->getMessage() . "\n";
    exit(1);
}
