
<?php
/**
 * register.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Initialize session
require_once 'include/session.php';

// Get required classes and configuration
require_once 'classes/reg.class.php';
require_once 'include/config.php';
require_once 'include/db.php';

// Create a new registration object
$new = new reg();

// Process registration step two if action is specified
$action = isset($_GET['action']) ? $_GET['action'] : "null";
if ($action == "step2") {
    // Start registration process
    $regme = new reg();
    $result = $regme->regUser();
    
    // Further processing could be done here based on $result
} else {
    // Show registration form
    $new->showform();
}
?>
