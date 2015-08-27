<?php
/**
 * searcharchive.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */

include '../include/error_handler.php';
$time_start = microtime();

ob_start();

// check if error logging is turned on 
if ($error_logging == 1){
 
  // use custom error handler 
 set_error_handler('error_handler');
} 
if ($display_errors == 1 ){
	error_reporting(defined('E_STRICT') ? E_ALL | E_STRICT : E_ALL);
}else {
	
// turn off error reporting 
  error_reporting(0);
}

// check if session is already started PHP >= 5.4.0
if(session_id() == '') {
    session_start();
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $uid = $_SESSION['uid'];
    $user = $_SESSION['username'];
    $form = "Welcome <a href='../u/$user'>" . $_SESSION['username'] . "</a>&nbsp;";
    $form .= "<a href='logout.php'>logout</a>";
    $uname = $_SESSION['username'];  
} else {
    $form = "<input type='hidden' name='login'>";
    $form .= "<input class='span2' type='text' name='username' placeholder='User name'>";
    $form .= "<input class='span2' type='password' name='password' placeholder='Password'>";
    $form .= "<input type='submit' name='submit' value='Login' class='btn'/>";
    $form .= "</form>";
    $form .= "<ul class='nav pull-right'>";
    $form .= "<li><a href='register.php'>Registration</a></li>";
}
//include_once ('../include/config.php');
//include_once ('../classes/conn.class.php');
if (isset($_POST['submit'])) {
    $cmd = new Conn();
    $cmd->login($_POST['username'], $_POST['password']);
    $location = $_SERVER['HTTP_REFERER'];
      header('Refresh:1; url=$location');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $config['site_name'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="rien">
    <meta name="keywords" content="rien"/>
    <meta name="author" content="Php-pastebin">

     <link href="../css/style.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
</head>

<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/"><?php echo $config['site_name'];?></a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <li><a href="../index.php">Add a new paste</a></li>
                    <li><a href="../archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="<?$_SERVER[’PHP_SELF’];?>" method="post"/>
                <?=$form;?></form>
                <ul class="nav pull-right">

                    <!----Registration button here-->


                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
<header class="jumbotron masthead" id="overview">

    </div>
</header>
<div class="container-fluid">


    <div class="row-fluid">
        <div class="span2 offset1">
            <div class="base-block">
                <div class="title">Paste Search</div>
                <form class="form-search" name="form1" method="get" action="../search.php">
                    <div class="input-prepend">

                        <input type="text" name="term" class="span12">
                    </div>
                </form>
            </div>
            <div class="base-block">
                <div class="title">Recent pastes</div>

                <ul class="nav nav-list">

                    <!--Recent Post go here -->
                    <li>
                        <?php include 'live.php';?>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">
            <div class="base-block">
                <div class="title"><?=$_GET['syntax'];?></div>

               <img src="../img/code.jpg" height="36" width="48">&nbsp; <font size="6"> Paste::Syntax:<b> <?=$_GET['syntax'];?></b></font>

                 <div class="c" style="font-family: monospace;">
                    <Br><Br>
                    <table width="100%" Height="" id="archvie" border="0">
                        <tr id="archive" bgcolor="">
                            <td nowrap width="25" id=""><span class="whitetext_md"><B>Name / Title</B><hr></td>
                            <td nowrap width="25"><span class="whitetext_md"><b>Posted</b><Hr></span></td>
                            <td nowrap width="25"><span class="whitetext_md"><b>Total Hits</b><Hr></span></td>
                            <td nowrap width="25"><span class="whitetext_md"><b>Syntax</b><hr></span></td>
                        </tr>
                        <tr> <?php include '../classes/search.class.php'; $search = new searcher(); $search->searchbysyntax();?>
                            <?php
                            $error = $search->error;
                            if (isset($error)) {
                                echo $error;
                            }
                            ?>
                            </tr>
                    </div>
                <!--/row-->
            </div>
            <!--/span-->
        </div>
        <!--/row-->

        <script type="text/javascript">
            $(document).ready(function () {
                $("[rel=tooltip]").tooltip();
            });
        </script>


        <footer class="span8">
            
        </footer>

    </div>
    <!-- /container -->
</body>
</html>

