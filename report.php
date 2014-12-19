<?php
  $postid = $_GET['pid'];
   include_once 'classes/main.class.php';
   $makereport = new main();
   $makereport->report($postid);
?>
