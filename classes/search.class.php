<?php
declare(strict_types=1);

/**
 * Search Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.1.0-modern
 */

class Search
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

        $stmt->bind_param('ssi', $searchTerm, $searchTerm, $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Search posts by syntax
     *
     * @param string $syntax
     * @param int $limit
     * @return array
     */
    public function searchBySyntax(string $syntax, int $limit = 50): array
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

        $stmt->bind_param('si', $syntax, $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Get available syntax options
     *
     * @return array
     */
    public function getSyntaxOptions(): array
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
     * Search posts by username
     *
     * @param string $username
     * @param int $limit
     * @return array
     */
    public function searchByUser(string $username, int $limit = 50): array
    {
        $posts = [];

        if (empty(trim($username))) {
            return $posts;
        }

        $limit = max(1, $limit);
        $stmt = $this->db->prepare(
            "SELECT * FROM public_post 
             WHERE posters_name = ? 
             ORDER BY post_date DESC 
             LIMIT ?"
        );

        $stmt->bind_param('si', $username, $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt->close();
        return $posts;
    }

    /**
     * Format search results for display
     *
     * @param array $posts
     * @return array
     */
    public function formatResults(array $posts): array
    {
        $formattedPosts = [];

        foreach ($posts as $post) {
            $post['formatted_date'] = (new DateTime($post['post_date']))->format('Y-m-d H:i');

            $textPreview = strip_tags($post['post_text']);
            $post['text_preview'] = (strlen($textPreview) > 150)
                ? mb_substr($textPreview, 0, 147) . '...'
                : $textPreview;

            $post['formatted_size'] = number_format((float) $post['post_size'], 2) . ' KB';

            $formattedPosts[] = $post;
        }

        return $formattedPosts;
    }
}
