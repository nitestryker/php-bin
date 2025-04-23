
<?php
declare(strict_types=1);

/**
 * index.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
*/

// Use improved session management
require_once 'include/session.php';
require_once 'include/config.php';
require_once 'classes/post.class.php';

// Get and validate action parameter
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'null';

if ($action === 'post') {
    try {
        $postHandler = new Post();
        $userType = $postHandler->loginCheck();

        switch ($userType) {
            case 'user':
                // Handle registered user post
                $connection = getDbConnection();
                $postId = $postHandler->RegUser();
                $_SESSION['postid'] = $postId;
                $postHandler->redirect();
                break;

            case 'guest':
                // Handle guest post
                $connection = getDbConnection();
                $postId = $postHandler->Guest();
                $_SESSION['postid'] = $postId;
                $postHandler->redirect();
                break;

            default:
                throw new RuntimeException('Invalid user type');
        }
    } catch (Exception $e) {
        error_log("Post error: " . $e->getMessage());
        header('Location: error.php?msg=' . urlencode('An error occurred while processing your post'));
        exit;
    }
}

/**
 * Get database connection
 * @return PDO Database connection
 * @throws RuntimeException if connection fails
 */
function getDbConnection(): PDO 
{
    global $dbhost, $dbusername, $dbpasswd, $database_name;
    
    try {
        $dsn = "mysql:host=$dbhost;dbname=$database_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, $dbusername, $dbpasswd, $options);
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new RuntimeException("Could not connect to database");
    }
}

// Include main template
require_once 'templates/main.tpl.php';
