
<?php
/**
 * Archive Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

class Archive
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
     * Get recent public posts
     *
     * @param int $limit Number of posts to return
     * @return array Recent posts
     */
    public function getRecentPosts($limit = 20)
    {
        $posts = [];
        
        $query = "SELECT * FROM public_post WHERE viewable = 1 ORDER BY post_date DESC LIMIT " . intval($limit);
        $result = $this->db->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }
        
        return $posts;
    }
    
    /**
     * Get popular public posts
     *
     * @param int $limit Number of posts to return
     * @return array Popular posts
     */
    public function getPopularPosts($limit = 20)
    {
        $posts = [];
        
        $query = "SELECT * FROM public_post WHERE viewable = 1 ORDER BY post_hits DESC LIMIT " . intval($limit);
        $result = $this->db->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }
        
        return $posts;
    }
    
    /**
     * Search public posts
     *
     * @param string $query Search query
     * @param int $limit Number of posts to return
     * @return array Search results
     */
    public function searchPosts($query, $limit = 50)
    {
        $posts = [];
        
        if (empty($query)) {
            return $posts;
        }
        
        $searchTerm = '%' . $this->db->real_escape_string($query) . '%';
        
        $stmt = $this->db->prepare(
            "SELECT * FROM public_post 
            WHERE (post_title LIKE ? OR post_text LIKE ?) 
            AND viewable = 1 
            ORDER BY post_date DESC 
            LIMIT ?"
        );
        
        $stmt->bind_param("ssi", $searchTerm, $searchTerm, $limit);
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
     * Get posts by syntax
     *
     * @param string $syntax Syntax filter
     * @param int $limit Number of posts to return
     * @return array Filtered posts
     */
    public function getPostsBySyntax($syntax, $limit = 50)
    {
        $posts = [];
        
        if (empty($syntax)) {
            return $posts;
        }
        
        $stmt = $this->db->prepare(
            "SELECT * FROM public_post 
            WHERE post_syntax = ? 
            AND viewable = 1 
            ORDER BY post_date DESC 
            LIMIT ?"
        );
        
        $stmt->bind_param("si", $syntax, $limit);
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
     * Get available syntax options
     *
     * @return array Syntax options
     */
    public function getSyntaxOptions()
    {
        $syntaxOptions = [];
        
        $query = "SELECT DISTINCT post_syntax FROM public_post WHERE viewable = 1 ORDER BY post_syntax";
        $result = $this->db->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $syntaxOptions[] = $row['post_syntax'];
            }
        }
        
        return $syntaxOptions;
    }
    
    /**
     * Get post statistics
     *
     * @return array Statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_posts' => 0,
            'total_public_posts' => 0,
            'total_private_posts' => 0,
            'total_syntax_options' => 0,
            'total_size' => 0
        ];
        
        // Get total posts
        $query = "SELECT COUNT(*) AS count FROM public_post";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_posts'] = $row['count'];
        }
        
        // Get public posts
        $query = "SELECT COUNT(*) AS count FROM public_post WHERE viewable = 1";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_public_posts'] = $row['count'];
        }
        
        // Get private posts
        $query = "SELECT COUNT(*) AS count FROM public_post WHERE viewable = 0";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_private_posts'] = $row['count'];
        }
        
        // Get syntax options
        $query = "SELECT COUNT(DISTINCT post_syntax) AS count FROM public_post";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total_syntax_options'] = $row['count'];
        }
        
        // Get total size
        $query = "SELECT SUM(CAST(post_size AS DECIMAL(10,2))) AS total_size FROM public_post";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc() && !is_null($row['total_size'])) {
            $stats['total_size'] = number_format($row['total_size'], 2);
        }
        
        return $stats;
    }
}
?>
