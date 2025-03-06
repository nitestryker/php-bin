<?php
/**
 * recent.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Include connection and classes
require_once 'config.php';
require_once 'db.php';

// Get recent posts
$query = "SELECT * FROM public_post WHERE viewable = 1 ORDER BY post_date DESC LIMIT 10";
$result = db_query($query);

if (db_num_rows($result) > 0) {
    echo '<table class="table table-striped">';
    echo '<thead>';
    echo '<tr><th>Title</th><th>Syntax</th><th>Date</th></tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($row = db_fetch_assoc($result)) {
        $post_title = htmlspecialchars($row['post_title']);
        $post_syntax = htmlspecialchars($row['post_syntax']);
        $post_date = htmlspecialchars($row['post_date']);
        $post_id = htmlspecialchars($row['postid']);

        echo '<tr>';
        echo '<td><a href="../' . $post_id . '">' . $post_title . '</a></td>';
        echo '<td>' . $post_syntax . '</td>';
        echo '<td>' . date('Y-m-d H:i', strtotime($post_date)) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No recent posts found.</p>';
}
?>