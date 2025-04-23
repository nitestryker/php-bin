<?php
declare(strict_types=1);

/**
 * Archive Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.1.0-modern
 */

class Archive
{
    private mysqli $db;
    private array $config;

    /**
     * Constructor
     *
     * @param mysqli $db
     * @param array $config
     */
    public function __construct(mysqli $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Get recent public posts
     *
     * @param int $limit
     * @return array
     */
    public function getRecentPosts(int $limit = 20): array
    {
        $posts = [];
        $limit = max(1, $limit); // prevent zero or negative limits

        $query = "SELECT * FROM public_post WHERE viewable = 1 ORDER BY post_date DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Get popular public posts
     *
     * @param int $limit
     * @return array
     */
    public function getPopularPosts(int $limit = 20): array
    {
        $posts = [];
        $limit = max(1, $limit);

        $query = "SELECT * FROM public_post WHERE viewable = 1 ORDER BY post_hits DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Search public posts
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchPosts(string $query, int $limit = 50): array
    {
        $posts = [];

        if (empty(trim($query))) {
            return $posts;
        }

        $searchTerm = '%' . $this->db->real_escape_string($query) . '%';
        $limit = max(1, $limit);

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
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Get posts by syntax
     *
     * @param string $syntax
     * @param int $limit
     * @return array
     */
    public function getPostsBySyntax(string $syntax, int $limit = 50): array
    {
        $posts = [];

        if (empty(trim($syntax))) {
            return $posts;
        }

        $limit = max(1, $limit);
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
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Get all distinct syntax options from public posts
     *
     * @return array
     */
    public function getSyntaxOptions(): array
    {
        $options = [];
        $query = "SELECT DISTINCT post_syntax FROM public_post WHERE viewable = 1 ORDER BY post_syntax";

        $result = $this->db->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $options[] = $row['post_syntax'];
            }
        }

        return $options;
    }

    /**
     * Get global post statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $stats = [
            'total_posts' => 0,
            'total_public_posts' => 0,
            'total_private_posts' => 0,
            'total_syntax_options' => 0,
            'total_size' => '0.00'
        ];

        $queries = [
            'total_posts'           => "SELECT COUNT(*) AS count FROM public_post",
            'total_public_posts'    => "SELECT COUNT(*) AS count FROM public_post WHERE viewable = 1",
            'total_private_posts'   => "SELECT COUNT(*) AS count FROM public_post WHERE viewable = 0",
            'total_syntax_options'  => "SELECT COUNT(DISTINCT post_syntax) AS count FROM public_post",
            'total_size'            => "SELECT SUM(CAST(post_size AS DECIMAL(10,2))) AS total_size FROM public_post"
        ];

        foreach ($queries as $key => $sql) {
            $result = $this->db->query($sql);
            if ($result && ($row = $result->fetch_assoc())) {
                $stats[$key] = $key === 'total_size'
                    ? number_format((float)($row['total_size'] ?? 0), 2)
                    : (int)$row['count'];
            }
        }

        return $stats;
    }
}
