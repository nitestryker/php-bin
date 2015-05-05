<?php
/**
 * error.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 *
*  When an error occurs such as a 404 this file will be displayed 
*/
error_reporting(0);
include 'include/config.php';
// if user is not logged in show login form at the top
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $uid = $_SESSION['uid'];
    $form = "Welcome <a href='profile.php?uid=$uid'>" . $_SESSION['username'] . "</a>&nbsp;";
    $form .= "<a href='index.php?action=logout'>logout</a>";
    $uname = $_SESSION['username'];
} else {
    $form = "<input class='span2' type='text' name='username' placeholder='User name'>";
    $form .= "<input class='span2' type='password' name='password' placeholder='Password'>";
    $form .= "<input type='submit' name='submit' value='Login' class='btn'/>";
    $form .= "</form>";
    $form .= "<ul class='nav pull-right'>";
    $form .= "<li><a href='../../register.php'>Registration</a></li>";
}


$pid = $_GET['pid'];

/*
 *
* Error Handling
*
*/
if (isset($_GET['usr'])) {
    $usr = $_GET['usr'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=$config['site_name'];?></title>
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
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script>
        function textAreaAdjust(o) {
            o.style.height = "1px";
            o.style.height = (25 + o.scrollHeight) + "px";
        }
    </script>
</head>

<body>

<script type="text/javascript" language="javascript">
    resizeIt = function () {
        var str = $('paste').value;
        var cols = $('paste').cols;

        var linecount = 0;
        $A(str.split("\n")).each(function (l) {
            linecount += Math.ceil(l.length / cols); // take into account long lines
        })
        $('paste').rows = linecount + 1;
    };

    Event.observe('paste', 'keyup', resizeIt); // you could attach to keyUp, etc if keydown doesn't work
    resizeIt(); //initial on load
</script>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/">Pastebin Clone</a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <li><a href="../index.php">Add a new paste</a></li>
                    <li><a href="../archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="index.php?action=login" method="post"/>
                <?=$form;?>
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
                <form class="form-search" name="form1" method="get" action="Search/">
                    <div class="input-prepend">

                        <input type="text" name="searchPaste" class="span12">
                        <input type="hidden" name="searchToken">
                    </div>
                </form>
            </div>
            <div class="base-block">
                <div class="title">Recent pastes</div>

                <ul class="nav nav-list">

                    <!--Recent Post go here -->
                    <li>
                        <? include 'include/view.php';?>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">
            <div class="base-block">
                <div class="title">Error - Not Found</div>

                <!--posted code goes here-->

                <div class="c" style="font-family: monospace;">
                    <center><img src="../img/sad.png" height=50 width=50>&nbsp; sorry but that users profile <font
                        color="red"><?=$usr;?></font> was not found <img src="../img/sad.png" height=50 width=50>
                        <center>
                            <center><b>please try again</b><Br>
                                <center></h1>


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
            <p>Pastebin Clone - developed by <a href="" target="_BLANK">Nitestryker Software</A></p>
        </footer>

    </div>
    <!-- /container -->
</body>
</html>