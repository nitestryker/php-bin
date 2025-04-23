<?php
/**
 * Post Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

declare(strict_types=1);

class Post 
{
    private ?mysqli $db;
    private array $config;
    private ?Main $main;

    public function __construct(?mysqli $db = null, ?array $config = null, ?Main $main = null) 
    {
        global $connection, $config as $globalConfig;

        $this->db = $db ?? $connection;
        $this->config = $config ?? $globalConfig;

        if ($main === null && class_exists('Main')) {
            $this->main = new Main($this->db, $this->config);
        } else {
            $this->main = $main;
        }
    }

    public function loginCheck(): string 
    {
        return (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) ? "user" : "guest";
    }

    public function regUserPost(): string|false 
    {
        try {
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                throw new RuntimeException('User not logged in');
            }

            $post_title = $this->main->clean($_POST['name'] ?? '');
            $post_syntax = $this->main->clean($_POST['syntax'] ?? '');
            $post_exp = intval($_POST['expi'] ?? 0);
            $exp_int = $post_exp;
            $exposure = $this->main->clean($_POST['expo'] ?? '');
            $post_text = $_POST['text'] ?? '';
            $posters_name = $_SESSION['username'] ?? 'guest';

            $post_title = empty($post_title) ? "untitled" : $post_title;
            $post_id = $this->main->generateRandomString(8);
            $users_ip = $this->main->getIp();
            $viewable = ($exposure == "Public") ? 1 : 0;
            $post_exp = $this->calculateExpiration($post_exp);

            date_default_timezone_set('UTC');
            $post_date = date('Y-m-d H:i:s');
            $post_size = number_format(strlen($post_text) / 1024, 2);
            $link = $this->config['site_url'] . "/" . $post_id;
            $slink = $this->main->shortLink($link);

            $stmt = $this->db->prepare(
                "INSERT INTO public_post 
                (postid, posters_name, ip, post_title, post_syntax, exp_int, post_exp, 
                post_text, post_date, post_size, post_hits, bitly, viewable) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $post_hits = 0;
            $stmt->bind_param(
                "ssssssssssssi",
                $post_id,
                $posters_name,
                $users_ip,
                $post_title,
                $post_syntax,
                $exp_int,
                $post_exp,
                $post_text,
                $post_date,
                $post_size,
                $post_hits,
                $slink,
                $viewable
            );

            if (!$stmt->execute()) {
                throw new RuntimeException("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION['postid'] = $post_id;
            return $post_id;

        } catch (Exception $e) {
            error_log("Post creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function guestPost(): string|false 
    {
        try {
            $post_title = $this->main->clean($_POST['name'] ?? '');
            $post_syntax = $this->main->clean($_POST['syntax'] ?? '');
            $post_exp = intval($_POST['expi'] ?? 0);
            $exp_int = $post_exp;
            $exposure = $this->main->clean($_POST['expo'] ?? '');
            $post_text = $_POST['text'] ?? '';
            $posters_name = 'guest';

            $post_title = empty($post_title) ? "untitled" : $post_title;
            $post_id = $this->main->generateRandomString(8);
            $users_ip = $this->main->getIp();
            $viewable = ($exposure == "Public") ? 1 : 0;
            $post_exp = $this->calculateExpiration($post_exp);

            date_default_timezone_set('UTC');
            $post_date = date('Y-m-d H:i:s');
            $post_size = number_format(strlen($post_text) / 1024, 2);
            $link = $this->config['site_url'] . "/" . $post_id;
            $slink = $this->main->shortLink($link);

            $stmt = $this->db->prepare(
                "INSERT INTO public_post 
                (postid, posters_name, ip, post_title, post_syntax, exp_int, post_exp, 
                post_text, post_date, post_size, post_hits, bitly, viewable) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $post_hits = 0;
            $stmt->bind_param(
                "ssssssssssssi",
                $post_id,
                $posters_name,
                $users_ip,
                $post_title,
                $post_syntax,
                $exp_int,
                $post_exp,
                $post_text,
                $post_date,
                $post_size,
                $post_hits,
                $slink,
                $viewable
            );

            if (!$stmt->execute()) {
                throw new RuntimeException("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION['postid'] = $post_id;
            return $post_id;

        } catch (Exception $e) {
            error_log("Guest post creation failed: " . $e->getMessage());
            return false;
        }
    }

    private function calculateExpiration(int $post_exp): string 
    {
        $date = new DateTime();

        switch($post_exp) {
            case 1: // 10 mins
                $date->modify("+10 minutes");
                break;
            case 2: // 1 hour
                $date->modify("+1 hour");
                break;
            case 3: // 24 hours
                $date->modify("+1 day");
                break;
            case 4: // 1 month
                $date->modify("+1 month");
                break;
            default:
                return "0000-00-00 00:00:00";
        }

        return $date->format('Y-m-d H:i:s');
    }

    public function redirect(): void 
    {
        if (isset($_SESSION['postid'])) {
            header("Location: {$_SESSION['postid']}", true, 303);
            exit();
        } else {
            header("Location: index.php", true, 303);
            exit();
        }
    }

    public function getPost(string $postId): array|false 
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM public_post WHERE postid = ?");
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("s", $postId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt->close();
                return false;
            }

            $post = $result->fetch_assoc();
            $stmt->close();

            // Update hit counter
            $this->db->query(
                "UPDATE public_post SET post_hits = post_hits + 1 WHERE postid = '" . 
                $this->db->real_escape_string($postId) . "'"
            );

            return $post;

        } catch (Exception $e) {
            error_log("Post retrieval failed: " . $e->getMessage());
            return false;
        }
    }

    public function deletePost(string $postId, string $username): bool 
    {
        try {
            $stmt = $this->db->prepare("SELECT posters_name FROM public_post WHERE postid = ?");
            if (!$stmt) {
                throw new RuntimeException("Database prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("s", $postId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt->close();
                return false;
            }

            $post = $result->fetch_assoc();
            $stmt->close();

            if ($post['posters_name'] !== $username && $username !== 'admin') {
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM public_post WHERE postid = ?");
            $stmt->bind_param("s", $postId);
            $result = $stmt->execute();
            $stmt->close();

            return $result;

        } catch (Exception $e) {
            error_log("Post deletion failed: " . $e->getMessage());
            return false;
        }
    }
}
?>