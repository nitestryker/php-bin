<?php
/**
 * report.php (report abuse)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
   $postid = $_GET['pid']; 
   $_SESSION[’reported’] = $postid; 
   include_once 'classes/main.class.php';
   $makereport = new main();
   $makereport->report($postid);
?>
