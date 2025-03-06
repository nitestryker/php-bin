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

class Post
{
    private $db;
    private $config;
    private $main;

    /**
     * Constructor
     *
     * @param object $db Database connection
     * @param array $config Configuration settings
     * @param object $main Main utility class
     */
    public function __construct($db = null, $config = null, $main = null)
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

    /**
     * Check if user is logged in
     *
     * @return string User type (user or guest)
     */
    public function loginCheck()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            return "user";
        } else {
            return "guest";
        }
    }

    /**
     * Create a post for a registered user
     *
     * @return string Post ID
     */
    public function regUserPost()
    {
        // Check if user is logged in
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return false;
        }

        // Get post data and sanitize
        $post_title = $this->main->clean($_POST['name'] ?? '');
        $post_syntax = $this->main->clean($_POST['syntax'] ?? '');
        $post_exp = intval($_POST['expi'] ?? 0);
        $exp_int = $post_exp;
        $exposure = $this->main->clean($_POST['expo'] ?? '');
        $post_text = $_POST['text'] ?? '';
        $posters_name = $_SESSION['username'] ?? 'guest';

        // If the title is blank call it untitled
        if (empty($post_title)) {
            $post_title = "untitled";
        }

        // Generate a random post id
        $post_id = $this->main->generateRandomString(8);

        // Get user's IP address
        $users_ip = $this->main->getIp();

        // Set visibility based on exposure setting
        $viewable = ($exposure == "Public") ? 1 : 0;

        // Calculate expiration date
        $post_exp = $this->calculateExpiration($post_exp);

        // Set post date
        date_default_timezone_set('UTC');
        $post_date = date('Y-m-d H:i:s');

        // Calculate post size
        $post_size = number_format(strlen($post_text) / 1024, 2);

        // Create link to be shortened
        $link = $this->config['site_url'] . "/" . $post_id;

        // Try to shorten with bit.ly
        $slink = $this->main->shortLink($link);

        // Prepare and execute query
        $stmt = $this->db->prepare("INSERT INTO public_post 
            (postid, posters_name, ip, post_title, post_syntax, exp_int, post_exp, 
             post_text, post_date, post_size, post_hits, bitly, viewable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $post_hits = 0;

        $stmt->bind_param("ssssssssssssi", 
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

        $result = $stmt->execute();

        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }

        $stmt->close();

        // Store post id in session
        $_SESSION['postid'] = $post_id;

        return $post_id;
    }

    /**
     * Create a post for a guest user
     *
     * @return string Post ID
     */
    public function guestPost()
    {
        // Same functionality as regUserPost but with guest settings
        // Get post data and sanitize
        $post_title = $this->main->clean($_POST['name'] ?? '');
        $post_syntax = $this->main->clean($_POST['syntax'] ?? '');
        $post_exp = intval($_POST['expi'] ?? 0);
        $exp_int = $post_exp;
        $exposure = $this->main->clean($_POST['expo'] ?? '');
        $post_text = $_POST['text'] ?? '';
        $posters_name = 'guest';

        // If the title is blank call it untitled
        if (empty($post_title)) {
            $post_title = "untitled";
        }

        // Generate a random post id
        $post_id = $this->main->generateRandomString(8);

        // Get user's IP address
        $users_ip = $this->main->getIp();

        // Set visibility based on exposure setting
        $viewable = ($exposure == "Public") ? 1 : 0;

        // Calculate expiration date
        $post_exp = $this->calculateExpiration($post_exp);

        // Set post date
        date_default_timezone_set('UTC');
        $post_date = date('Y-m-d H:i:s');

        // Calculate post size
        $post_size = number_format(strlen($post_text) / 1024, 2);

        // Create link to be shortened
        $link = $this->config['site_url'] . "/" . $post_id;

        // Try to shorten with bit.ly
        $slink = $this->main->shortLink($link);

        // Prepare and execute query
        $stmt = $this->db->prepare("INSERT INTO public_post 
            (postid, posters_name, ip, post_title, post_syntax, exp_int, post_exp, 
             post_text, post_date, post_size, post_hits, bitly, viewable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $post_hits = 0;

        $stmt->bind_param("ssssssssssssi", 
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

        $result = $stmt->execute();

        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }

        $stmt->close();

        // Store post id in session
        $_SESSION['postid'] = $post_id;

        return $post_id;
    }

    /**
     * Calculate expiration date based on selected option
     *
     * @param int $post_exp Expiration option (1-4)
     * @return string Expiration date in Y-m-d H:i:s format
     */
    private function calculateExpiration($post_exp)
    {
        $date = new DateTime();

        switch($post_exp) {
            // 10 mins
            case 1:
                $date->modify("+10 minutes");
                break;
            // 1 hour 
            case 2:
                $date->modify("+1 hour");
                break;
            // 24 hours 
            case 3:
                $date->modify("+1 day");
                break;
            // 1 month 
            case 4:
                $date->modify("+1 month");
                break;
            // No expiration
            default:
                return "0000-00-00 00:00:00";
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Redirect to the post page
     */
    public function redirect()
    {
        if (isset($_SESSION['postid'])) {
            $post_id = $_SESSION['postid'];
            header("Location: {$post_id}");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }
    }

    /**
     * Get a post by ID
     *
     * @param string $postId Post ID
     * @return array|false Post data or false if not found
     */
    public function getPost($postId)
    {
        $stmt = $this->db->prepare("SELECT * FROM public_post WHERE postid = ?");
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
        $this->db->query("UPDATE public_post SET post_hits = post_hits + 1 WHERE postid = '" . 
                        $this->db->escape($postId) . "'");

        return $post;
    }

    /**
     * Delete a post
     *
     * @param string $postId Post ID
     * @param string $username Username
     * @return bool Success or failure
     */
    public function deletePost($postId, $username)
    {
        // Only allow deletion by the post owner or admin
        $stmt = $this->db->prepare("SELECT posters_name FROM public_post WHERE postid = ?");
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

        // Delete the post
        $stmt = $this->db->prepare("DELETE FROM public_post WHERE postid = ?");
        $stmt->bind_param("s", $postId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
?>