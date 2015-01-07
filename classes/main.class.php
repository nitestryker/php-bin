<?php
/**
 * main.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */

class main
{
    //  not loggged-in redirect to front page
    function nologin()
    {
        echo "you must login first";
        include 'include/config.php';
        $index = $config['site_index'];
        header("refresh:3;url=/$index");
    }


    function logout()
    {

        // Unset all of the session variables.
        $_SESSION = array();
        // Finally, destroy the session.
        session_destroy();
        unset($_SESSION["username"]);
    }


    function clean($var = null)
    {
        // sanitation
        $var = htmlspecialchars($var);
        $var = trim(htmlspecialchars($var, ENT_QUOTES, "utf-8"));
        $var = mysql_real_escape_string($var);
        $var = strip_tags($var);
        return $var;
    }
function redirect()
    {
        // include config file get site folder index
        include 'include/config.php';
        $index = $config['site_index'];
        $location = $_SERVER['HTTP_REFERER'];
        $index = $config['site_index'];
        header("refresh: 6; url=/$index/$location");
    }
function report($post = null) 
 {
    $post = $post;
    include 'include/config.php';
    $site = $config['site_url'];
    $email = $config['site_admin_email'];
   $to = $email;
   $subject = "post #$post was reported";
   $message = "post #$post was reported as a violation";
   $from = "abuse@$site";
   $headers = "From: $from";
   mail($to,$subject,$message,$headers);
 }

}

// end of class

?>
