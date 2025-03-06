
<?php
/**
 * Search Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

class Search
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
     * Search posts by syntax
     *
     * @param string $syntax Syntax filter
     * @param int $limit Number of posts to return
     * @return array Filtered posts
     */
    public function searchBySyntax($syntax, $limit = 50)
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
                if (!empty($row['post_syntax'])) {
                    $syntaxOptions[] = $row['post_syntax'];
                }
            }
        }
        
        return $syntaxOptions;
    }
    
    /**
     * Search posts by user
     *
     * @param string $username Username to search for
     * @param int $limit Number of posts to return
     * @return array User's posts
     */
    public function searchByUser($username, $limit = 50)
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
     * Format search results for display
     *
     * @param array $posts Posts to format
     * @return array Formatted posts
     */
    public function formatResults($posts)
    {
        $formattedPosts = [];
        
        foreach ($posts as $post) {
            // Format date
            $date = new DateTime($post['post_date']);
            $post['formatted_date'] = $date->format('Y-m-d H:i');
            
            // Truncate text preview
            $textPreview = strip_tags($post['post_text']);
            $post['text_preview'] = (strlen($textPreview) > 150) 
                ? substr($textPreview, 0, 147) . '...' 
                : $textPreview;
            
            // Format post size
            $post['formatted_size'] = number_format($post['post_size'], 2) . ' KB';
            
            $formattedPosts[] = $post;
        }
        
        return $formattedPosts;
    }
}
?>
