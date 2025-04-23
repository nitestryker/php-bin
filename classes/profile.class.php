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

declare(strict_types=1);

class Profile
{
    private mysqli $db;
    private array $config;
    
    public function __construct(mysqli $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    public function getUserProfile(string $username): array|false
    {
        try {
            if (empty($username)) {
                return false;
            }
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

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

        } catch (Exception $e) {
            error_log("Profile retrieval failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserPosts(string $username, int $limit = 20): array
    {
        try {
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
            
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

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

        } catch (Exception $e) {
            error_log("User posts retrieval failed: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateProfile(string $username, array $data): bool
    {
        try {
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
                $_SESSION['username'] !== $username) {
                return false;
            }
            
            $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new InvalidArgumentException("Invalid email address");
            }
            
            $fullname = htmlspecialchars($data['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
            $website = filter_var($data['website'] ?? '', FILTER_VALIDATE_URL);
            $bio = htmlspecialchars($data['bio'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $stmt = $this->db->prepare(
                "UPDATE users 
                SET email = ?, fullname = ?, website = ?, bio = ? 
                WHERE username = ?"
            );

            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("sssss", $email, $fullname, $website, $bio, $username);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;

        } catch (Exception $e) {
            error_log("Profile update failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function changePassword(string $username, string $currentPassword, string $newPassword): bool
    {
        try {
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
                $_SESSION['username'] !== $username) {
                return false;
            }
            
            $stmt = $this->db->prepare("SELECT password FROM users WHERE username = ?");
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $stmt->close();
                return false;
            }
            
            $userData = $result->fetch_assoc();
            $stmt->close();
            
            if (!password_verify($currentPassword, $userData['password'])) {
                return false;
            }
            
            if (strlen($newPassword) < 8) {
                throw new InvalidArgumentException("Password must be at least 8 characters");
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE username = ?");
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("ss", $hashedPassword, $username);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;

        } catch (Exception $e) {
            error_log("Password change failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function uploadAvatar(string $username, array $file): bool
    {
        try {
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
                $_SESSION['username'] !== $username) {
                return false;
            }
            
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                throw new RuntimeException("No file uploaded");
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new RuntimeException("Invalid file type");
            }
            
            if ($file['size'] > 500000) {
                throw new RuntimeException("File too large");
            }
            
            $avatarDir = dirname(__FILE__) . '/../img/avatars/';
            $avatarName = $username . '_' . time() . '.jpg';
            $avatarPath = $avatarDir . $avatarName;
            
            if (!is_dir($avatarDir)) {
                if (!mkdir($avatarDir, 0755, true)) {
                    throw new RuntimeException("Failed to create avatar directory");
                }
            }
            
            $success = $this->resizeAndSaveImage($file['tmp_name'], $avatarPath, 200, 200);
            
            if ($success) {
                $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE username = ?");
                if (!$stmt) {
                    throw new RuntimeException("Database prepare failed: " . $this->db->error);
                }

                $stmt->bind_param("ss", $avatarName, $username);
                $result = $stmt->execute();
                $stmt->close();
                
                return $result;
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Avatar upload failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function resizeAndSaveImage(string $sourcePath, string $destPath, int $width, int $height): bool
    {
        try {
            $sourceInfo = getimagesize($sourcePath);
            if (!$sourceInfo) {
                throw new RuntimeException("Invalid image file");
            }
            
            $sourceImage = match ($sourceInfo[2]) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
                IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
                default => throw new RuntimeException("Unsupported image type")
            };
            
            if (!$sourceImage) {
                throw new RuntimeException("Failed to create source image");
            }
            
            $targetImage = imagecreatetruecolor($width, $height);
            if (!$targetImage) {
                throw new RuntimeException("Failed to create target image");
            }
            
            if (!imagecopyresampled(
                $targetImage, $sourceImage,
                0, 0, 0, 0,
                $width, $height, $sourceInfo[0], $sourceInfo[1]
            )) {
                throw new RuntimeException("Failed to resize image");
            }
            
            $result = imagejpeg($targetImage, $destPath, 90);
            
            imagedestroy($sourceImage);
            imagedestroy($targetImage);
            
            return $result;

        } catch (Exception $e) {
            error_log("Image processing failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserStats(string $username): array
    {
        try {
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
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $stats['total_posts'] = $row['count'];
            }
            $stmt->close();
            
            // Get public posts
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM public_post WHERE posters_name = ? AND viewable = 1"
            );
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $stats['public_posts'] = $row['count'];
            }
            $stmt->close();
            
            // Get private posts
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM public_post WHERE posters_name = ? AND viewable = 0"
            );
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $stats['private_posts'] = $row['count'];
            }
            $stmt->close();
            
            // Get total views
            $stmt = $this->db->prepare(
                "SELECT SUM(post_hits) as total_views FROM public_post WHERE posters_name = ?"
            );
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

        } catch (Exception $e) {
            error_log("Stats retrieval failed: " . $e->getMessage());
            return $stats;
        }
    }
}
?>
