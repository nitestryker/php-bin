
<?php
declare(strict_types=1);

/**
 * avatar.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.1.0
 */

require_once 'config.php';
require_once 'db.php';

try {
    $uimage = filter_input(INPUT_GET, 'uimage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (!$uimage) {
        throw new RuntimeException('Invalid username provided');
    }

    $stmt = db_prepare("SELECT avatar FROM users WHERE username = ?");
    $stmt->bind_param("s", $uimage);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (empty($row['avatar'])) {
        $defaultImages = [
            "https://secure.gravatar.com/avatar/default1?s=200",
            "https://secure.gravatar.com/avatar/default2?s=200",
            "https://secure.gravatar.com/avatar/default3?s=200",
            "https://secure.gravatar.com/avatar/default4?s=200",
            "https://secure.gravatar.com/avatar/default5?s=200"
        ];

        $randomImage = $defaultImages[array_rand($defaultImages)];
        header('Content-Type: image/jpeg');
        readfile($randomImage);
        exit;
    }

    header('Content-Type: image/jpeg');
    echo $row['avatar'];

} catch (Exception $e) {
    error_log("Avatar error: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}
