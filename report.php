<?php
   $postid = $_GET['pid']; 
   $_SESSION[’reported’] = $postid; 
   include_once 'classes/main.class.php';
   $makereport = new main();
   $makereport->report($postid);
?>
