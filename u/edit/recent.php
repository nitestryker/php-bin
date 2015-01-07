<?php
/**
 * recent.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
?>
<html>
<head>
    <style>
        a href {
            text-color: green;
        }

        #status {

        }

    </style>
</head>
<?php
error_reporting(0);
include_once '../../include/config.php';


 $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
     or die ("Couldn't connect to server.");
   	    $db = mysql_select_db("$database_name", $connection)
               or die("Couldn't select database.");

$sql = "SELECT * FROM  public_post ORDER BY post_date DESC LIMIT 15";


$result = mysql_query($sql);
/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
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
while ($row = mysql_fetch_array($result)) {
    // variables from table
    $postid = $row['public_postid'];
    $post_title = $row['post_title'];
    $post_date = $row['post_date'];
    $post_id = $row['postid'];
    $post_syntax = $row['post_syntax'];
    $ps = "[$post_syntax]";
    $syntax = $post_syntax;
    if ($post_syntax = "") {
        $post_syntax = null;
    }
    $post_hits = $row['post_hits'];
    if (empty($post_hits)) {
        $post_hits = "0";
        $count = substr_count($post_text, "\n");
    }
    $my_time = strtotime($post_date);
    $postdate = time_since($my_time);
    echo "<i class='icon-file'><img src='http://icons.iconarchive.com/icons/semlabs/web-blog/48/post-remove-icon.png' height='20' width='20'>&nbsp; &nbsp;<a href='../../$post_id'> </i>$post_title</a><br>&nbsp;&nbsp;&nbsp;&nbsp;<small>$postdate ago</small>";
    echo "<br>";
    echo "<i><span id='status' class='unreviewed'>&nbsp; &nbsp;<small> viewed <b>$post_hits</b> times</small></span></class></span><br><br>";
}
 if (empty($postid)) {
     echo "<i><span id='status' class='unreviewed'>&nbsp; &nbsp;&nbsp;&nbsp;<large><font color='black'><i>No recent paste</font></large></span></class></span><br><br>";
 }
?>