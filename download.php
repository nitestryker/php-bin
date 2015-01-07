<?php
/**
 * download.php (download to text file)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
*/
error_reporting(E_ALL);
// get the post id 
$postid = $_GET['pid'];


 function download($postid = null){
     // get needed files 
        include_once 'include/config.php';
  
    // sanitation
        $postid = htmlspecialchars($postid);
        $postid = trim(htmlspecialchars($postid, ENT_QUOTES, "utf-8"));
   
  
   // make connection to DB
    include_once 'include/config.php';  
 $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");
    // SQL query 
     $sql = "SELECT * FROM public_post WHERE postid = $postid";
        $result = mysql_query($sql);
        while ($row = mysql_fetch_array($result)) 
        {
         $post_id = $row['postid'];
         $post_text = $row['post_text'];
         $post_title = $row['post_title'];

        }
       
          // make download 
           $filecontent=$post_text; 
           $downloadfile="$post_title.txt";
           header("Content-Type: plain/text"); 
           header("Content-disposition: attachment; filename=$downloadfile"); 
           header("Content-Transfer-Encoding: binary"); 
           header("Pragma: no-cache"); 
           header("Expires: 0");
           echo"$filecontent";
    } // end of function
     download($postid);
?>