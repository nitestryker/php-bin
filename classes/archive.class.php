<?php
/**
 * archive.class.php
 *
 * @package PHP-Bin
 * @auther Nitestryker
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */

class archive
{
    function getarchive($page = null)
    {
        include 'include/config.php';
        $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        $this->page = $page;
        if (isset($this->page)) {
            $this->page = $this->page;
        } else {
            $this->page = 1;
        }
        $this->start_from = ($this->page - 1) * 20;
        $sql = "SELECT * FROM public_post  ORDER BY post_date DESC LIMIT $this->start_from, 20";
        $rs_result = mysql_query($sql, $connection);
        while ($row = mysql_fetch_assoc($rs_result)) {
            echo "<tr>";
            $postid = $row['postid'];
            $post_title = $row['post_title'];
            $post_hits = $row['post_hits'];
            echo "<td><img src='http://icons.iconarchive.com/icons/semlabs/web-blog/48/post-remove-icon.png' height='18' width='18'>&nbsp; <a href='$postid'>$post_title</a><hr></td>";
            $my_time = strtotime($row['post_date']);
            $postdate = $this->time_since($my_time);
            echo "<td>$postdate ago<hr></td>";
            echo "<td>$post_hits<hr></td>";
            $syn = $row['post_syntax'];
            $syntax = "<a href='archive/$syn'>$syn</a>";
            echo "<td>$syntax<hr></td>";
            echo "</tr>";
        }
        echo "</table>";

        $sql = "SELECT COUNT(public_postid) FROM public_post";
        $rs_result = mysql_query($sql, $connection);
        $row = mysql_fetch_row($rs_result);
        $total_records = $row[0];
        $total_pages = ceil($total_records / 20);

        for ($i = 1; $i <= $total_pages; $i++) {
            echo "Page <a href='archive.php?page=" . $i . "'>" . $i . "</a> ";
        }
    } //end of function


    // time since
    function time_since($original)
    {
        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
        );

        $today = time(); /* Current unix time  */
        $since = $today - $original;

        // $j saves performing the count function each time around the loop
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {

            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0) {
                // DEBUG print "<!-- It's $name -->\n";
                break;
            }
        }


        $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";

        /*
        * second half of the time not needed here
        *
        */
        // if ($i + 1 < $j) {
        // now getting the second item
        //  $seconds2 = $chunks[$i + 1][0];
        //$name2 = $chunks[$i + 1][1];

        // add second item if it's greater than 0
        // if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
        //  $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        //  }
        //  }

        return $print;
    }
} // end of class
?>