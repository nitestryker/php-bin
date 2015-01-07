<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
   <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script>
var doPrintPage;

function printPage(){
    window.print();
    // this does not work for Chrom Version 34.0.1847.116 
    window.onfocus=function(){ window.close();}
}

$(document).ready(function(){
    $('input').blur(function(){
        //3sec after the user leaves the input, printPage will fire
        doPrintPage = setTimeout('printPage();', 3000);
    });
    $('input').focus(function(){
        //But if another input gains focus printPage won't fire
        clearTimeout(doPrintPage);
    });
});
</script>
  </head>
 <body onload="printPage()">

  </body>
</html>
<?php
/**
 * print.php (easy print post)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
ob_start();
error_reporting(E_ALL);

$rid = $_GET['rid'];
if ($rid == "") {
    redirect();
}
$rid = htmlspecialchars($rid);
$rid = trim(htmlspecialchars($rid, ENT_QUOTES, "utf-8"));

// make connection to database
require '../include/config.php';
$database_name = $database_name;
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
    or die ("Couldn't connect to server.");
$db = mysql_select_db("$database_name", $connection)
    or die("Couldn't select database.");

$sql = "SELECT * FROM public_post WHERE postid = $rid";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $post_text = $row['post_text'];

}
if ($post_text == "") {
    echo "Sorry &nbsp;<b>$rid</b> was not found, Please try again";
    exit();
}
include_once '../include/geshi.php';
$syntax = "text";
$geshi = new GeSHi($post_text, $syntax);
echo $geshi->parse_code();
mysql_close($connection);
function redirect()
{
    header('refresh:0; url=index.php');
    include 'index.php';
}

?>