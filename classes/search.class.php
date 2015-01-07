<?php
/**
 * search.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
error_reporting(0);
class searcher
{
    function searchbyname()
    {
        //include config
        include 'include/config.php';

        // make connection to database
        $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // sanitize request
        $term = mysql_real_escape_string($_REQUEST['term']);
        $term = addslashes($term);
        $term = strip_tags($term);
        if ($term == "") {
            echo "<tr><td><font size='6'><font color='red'>sorry no results found</font></td><tr>";
            exit();
        }
        $sql = mysql_query("SELECT * FROM public_post WHERE post_title LIKE '%" . $term . "%' AND viewable = 1") or die
        (mysql_error());
        while ($row = mysql_fetch_array($sql)) {
            $postid = $row['postid'];
            $post_title = $row['post_title'];
            $post_syntax = $row['post_syntax'];
            $post_date = $row['post_date'];
             $post_hits = $row['post_hits'];

        }

        // if not results are found display error otherwize display results
        if (empty($post_title)) {
            $this->error = "<td><font size='6'><font color='red'>sorry no results found</font></td><tr>";
            $pd = null;
        } else {
            echo "<tr>";
            echo  "<td><img src='http://icons.iconarchive.com/icons/semlabs/web-blog/48/post-remove-icon.png' height='18' width='18'>&nbsp;<a href='$postid'>$post_title</a><hr></td>";
            echo "<td>";
            $my_time = strtotime($post_date);
            $post_date = $this->time_since($my_time);
            $pd = "$post_date ago<hr></td>";
            echo $pd;
            echo "<td>$post_hits<hr></td>";
            echo "<td>$post_syntax<hr></td>";
            echo "</tr>";
        }
        if ($post_title == "") {
            $this->error = "<td><font size='4'><font color='red'>sorry no results found</font></td><tr>";
            $pd = null;
        }

    } // end of function
    
    
    
     /* search for post 
      */
    
     function searchbysyntax()
    {
        //include config
        include '../include/config.php';

        // make connection to database
        $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // sanitize request
        $term =$_GET['syntax'];
        $term = addslashes($term);
        $term = strip_tags($term);
        if ($term == "") {
            echo "<tr><td><font size='6'><font color='red'>sorry no results found</font></td><tr>";
            exit();
        }
        $sql = mysql_query("SELECT * FROM public_post WHERE post_syntax = '$term' ") or die
        (mysql_error());
        while ($row = mysql_fetch_array($sql)) {
            $postid = $row['postid'];
            $post_title = $row['post_title'];
            $post_syntax = $row['post_syntax'];
            $post_date = $row['post_date'];
            $post_hits = $row['post_hits'];

        // if not results are found display error otherwize display results
        if (empty($post_title)) {
            $this->error = "<td><font size='6'><font color='red'>sorry no results found</font></td><tr>";
            $pd = null;
        } else {
            echo "<tr>";
            echo  "<td><img src='http://icons.iconarchive.com/icons/semlabs/web-blog/48/post-remove-icon.png' height='18' width='18'>&nbsp;<a href='../$postid'>$post_title</a><hr></td>";
            echo "<td>";
            $my_time = strtotime($post_date);
            $post_date = $this->time_since($my_time);
            $pd = "$post_date ago<hr></td>";
            echo $pd;
            echo "<td>$post_hits<hr></td>";
            echo "<td>$post_syntax<hr></td>";
            echo "</tr>";
        }
        if ($post_title == "") {
            $this->error = "<td><font size='4'><font color='red'>sorry no results found</font></td><tr>";
            $pd = null;
        }
	}

    } // end of function

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
}

// end of class
?>
