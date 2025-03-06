
<?php
/**
 * Profile Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

class Profile
{
    private $db;
    private $config;
    
    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     * @param array $config Configuration settings
     */
    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * Get user profile
     *
     * @param string $username Username
     * @return array|bool User data or false if not found
     */
    public function getUserProfile($username)
    {
        if (empty($username)) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }
        
        $userData = $result->fetch_assoc();
        $stmt->close();
        
        // Remove sensitive data
        unset($userData['password']);
        
        return $userData;
    }
    
    /**
     * Get user's posts
     *
     * @param string $username Username
     * @param int $limit Number of posts to return
     * @return array User's posts
     */
    public function getUserPosts($username, $limit = 20)
    {
        $posts = [];
        
        if (empty($username)) {
            return $posts;
        }
        
        $stmt = $this->db->prepare(
            "SELECT * FROM public_post 
            WHERE posters_name = ? 
            ORDER BY post_date DESC 
            LIMIT ?"
        );
        
        $stmt->bind_param("si", $username, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }
        
        $stmt->close();
        
        return $posts;
    }
    
    /**
     * Update user profile
     *
     * @param string $username Username
     * @param array $data Profile data to update
     * @return bool Success or failure
     */
    public function updateProfile($username, $data)
    {
        // Verify user is logged in and is updating their own profile
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
            $_SESSION['username'] !== $username) {
            return false;
        }
        
        // Sanitize and validate data
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return false;
        }
        
        $fullname = htmlspecialchars($data['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
        $website = filter_var($data['website'] ?? '', FILTER_VALIDATE_URL);
        $bio = htmlspecialchars($data['bio'] ?? '', ENT_QUOTES, 'UTF-8');
        
        // Update profile
        $stmt = $this->db->prepare("UPDATE users SET email = ?, fullname = ?, website = ?, bio = ? WHERE username = ?");
        $stmt->bind_param("sssss", $email, $fullname, $website, $bio, $username);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Change user password
     *
     * @param string $username Username
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return bool Success or failure
     */
    public function changePassword($username, $currentPassword, $newPassword)
    {
        // Verify user is logged in and is updating their own profile
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
            $_SESSION['username'] !== $username) {
            return false;
        }
        
        // Get current user data
        $stmt = $this->db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }
        
        $userData = $result->fetch_assoc();
        $stmt->close();
        
        // Verify current password
        if (!password_verify($currentPassword, $userData['password'])) {
            return false;
        }
        
        // Check new password length
        if (strlen($newPassword) < 8) {
            return false;
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $username);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Upload avatar
     *
     * @param string $username Username
     * @param array $file Uploaded file data
     * @return bool Success or failure
     */
    public function uploadAvatar($username, $file)
    {
        // Verify user is logged in and is updating their own profile
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
            $_SESSION['username'] !== $username) {
            return false;
        }
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Check file size (max 500kb)
        if ($file['size'] > 500000) {
            return false;
        }
        
        // Generate avatar filename
        $avatarDir = dirname(__FILE__) . '/../img/avatars/';
        $avatarName = $username . '_' . time() . '.jpg';
        $avatarPath = $avatarDir . $avatarName;
        
        // Create directory if it doesn't exist
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }
        
        // Resize and save image
        $success = $this->resizeAndSaveImage($file['tmp_name'], $avatarPath, 200, 200);
        
        if ($success) {
            // Update avatar in database
            $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE username = ?");
            $stmt->bind_param("ss", $avatarName, $username);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * Resize and save image
     *
     * @param string $sourcePath Source image path
     * @param string $destPath Destination image path
     * @param int $width Target width
     * @param int $height Target height
     * @return bool Success or failure
     */
    private function resizeAndSaveImage($sourcePath, $destPath, $width, $height)
    {
        // Get image info
        $sourceInfo = getimagesize($sourcePath);
        if (!$sourceInfo) {
            return false;
        }
        
        // Create source image
        switch ($sourceInfo[2]) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        // Create target image
        $targetImage = imagecreatetruecolor($width, $height);
        
        // Resize image
        imagecopyresampled(
            $targetImage, $sourceImage,
            0, 0, 0, 0,
            $width, $height, $sourceInfo[0], $sourceInfo[1]
        );
        
        // Save image
        $result = imagejpeg($targetImage, $destPath, 90);
        
        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($targetImage);
        
        return $result;
    }
    
    /**
     * Get user statistics
     *
     * @param string $username Username
     * @return array User statistics
     */
    public function getUserStats($username)
    {
        $stats = [
            'total_posts' => 0,
            'public_posts' => 0,
            'private_posts' => 0,
            'popular_post' => null,
            'total_views' => 0,
            'join_date' => '',
            'last_active' => ''
        ];
        
        if (empty($username)) {
            return $stats;
        }
        
        // Get total posts
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM public_post WHERE posters_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['total_posts'] = $row['count'];
        }
        $stmt->close();
        
        // Get public posts
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM public_post WHERE posters_name = ? AND viewable = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['public_posts'] = $row['count'];
        }
        $stmt->close();
        
        // Get private posts
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM public_post WHERE posters_name = ? AND viewable = 0");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['private_posts'] = $row['count'];
        }
        $stmt->close();
        
        // Get total views
        $stmt = $this->db->prepare("SELECT SUM(post_hits) as total_views FROM public_post WHERE posters_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc() && !is_null($row['total_views'])) {
            $stats['total_views'] = $row['total_views'];
        }
        $stmt->close();
        
        // Get most popular post
        $stmt = $this->db->prepare(
            "SELECT postid, post_title, post_hits FROM public_post 
            WHERE posters_name = ? 
            ORDER BY post_hits DESC 
            LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['popular_post'] = $row;
        }
        $stmt->close();
        
        // Get user account info
        $stmt = $this->db->prepare("SELECT created, last_login FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['join_date'] = $row['created'];
            $stats['last_active'] = $row['last_login'];
        }
        $stmt->close();
        
        return $stats;
    }
}
?>
