<?php
declare(strict_types=1);

/**
 * Profile Template
 * @package PHP-Bin
 * @version 2.0.0
 */

require_once '../include/config.php';

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true, 
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Get profile username from URL param
$profile = htmlspecialchars($_GET['usr'] ?? '', ENT_QUOTES);

// Initialize variables
$form = '';
$uname = '';
$uid = null;
$user = '';
$edit = '';

// Handle logged in state
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $uid = $_SESSION['uid'] ?? null;
    $user = $_SESSION['username'] ?? '';
    $uname = $user;

    $form = sprintf(
        'Welcome <a href="../u/%s">%s</a>&nbsp;<a href="../logout.php">logout</a>',
        htmlspecialchars($user, ENT_QUOTES),
        htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES)
    );
} else {
    $form = <<<HTML
        <input type="hidden" name="login">
        <input class="span2" type="text" name="username" placeholder="User name">
        <input class="span2" type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Login" class="btn"/>
        </form>
        <ul class="nav pull-right">
        <li><a href="register.php">Registration</a></li>
HTML;
}

// Handle login submission
if (isset($_POST['submit'])) {
    try {
        require_once 'classes/conn.class.php';
        $cmd = new Conn();
        $cmd->login(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );
        $location = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Refresh: 1; url=' . htmlspecialchars($location, ENT_QUOTES));
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
    }
}

// Initialize profile data variables with safe defaults
$num_rows = 0;
$thits = 0;
$join_date = '';
$location = '';
$website = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <?php
    $site_name = isset($config) && isset($config['site_name']) ? $config['site_name'] : 'Site Name'; //Handle potential null
    ?>
    <title><?=$site_name;?></title>
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
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#load_recent").load('include/live.php').fadeIn("slow");
        }, 0); // refresh every 10000 milliseconds
    </script>
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
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('paste').addEventListener('keyup', resizeIt);
    });
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
            <a class="brand" href="/"></a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li><a href="../index.php">Add a new paste</a></li>
                    <li><a href="../archive.php">View all pastes</a></li>
                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="../index.php" method="post"/>
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
                <form class="form-search" name="form1" method="post" action="../search.php">
                    <div class="input-prepend">
                        <input type="hidden" name="action" value="name">
                        <input type="text" name="term" class="span12">
                    </div>
                </form>
            </div>
            <div class="base-block">
                <div class="title">Recent pastes</div>
                <ul class="nav nav-list">
                    <!--Recent Post go here -->
                    <li>
                        <div class="load_recent">
                            <?include '../u/live.php';?>
                        </div>
                    </li>
                </ul>
            </div>
            <!--/base-block-->
        </div>
        <div class="span8">
            <div class="base-block">
                <img src="../include/avatar.php?uimage=<?=$profile;?>" height="80" width="80">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <i><b><font size="5"><?=$uname;?>'s bin</font></b></i>
                <div class="edit"
                     style='position:absolute;left:830px;top:10px;width:582px;height:48px;z-index:36;padding:0;'><?=$edit;?></div>
                <?php
                $num_rows = isset($num_rows)? $num_rows : 0; // Handle potential null
                $thits = isset($thits)? $thits : 0; // Handle potential null
                $join_date = isset($join_date)? $join_date : ''; // Handle potential null
                $location = isset($location)? $location : ''; // Handle potential null
                $website = isset($website)? $website : ''; // Handle potential null
                ?>
                <div class="info"
                     style='position:relative;left:100px;top:-10px;width:612px;height:48px;z-index:36;padding:0;'>Total
                    Paste: <?=$num_rows;?> &nbsp; |&nbsp; Total Hits: <?=$thits;?> &nbsp; |&nbsp;
                    Joined: <?=$join_date;?> &nbsp; |&nbsp; Location: <?=$location?> &nbsp; |&nbsp;
                    Website: <?=$website;?>&nbsp; |
                </div>
                <!--posted code goes here-->
                <br>
                <br>
                <br>
                <table width="100%" Height="145" id="archvie" border="0">
                    <tr id="archive" bgcolor="">
                        <td nowrap width="45"  id=""><span class="whitetext_md"><B>&nbsp;Name / Title</B><hr></td>
                        <td nowrap width="45"><span class="whitetext_md"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Added</b><Hr></span></td>
                        <td nowrap width="45" ><span class="whitetext_md"><b>Expires</b><hr></span></td>
                        <td nowrap width="45"><span class="whitetext_md"><b>Hits</b><hr></span></td>
                        <td nowrap width="45"><span class="whitetext_md"><b>Syntax</b><hr></span></td>
                    </tr>
                    <tr><?$userpost = new profile(); $userpost->userspost($profieid)?>
                    </tr>
                </table>
                <Br><Br>
            </div>
            <!--/row-->
        </div>
        <!--/span-->
    </div>
    <!--/row-->
    <br>
</div>
<!-- /container -->
</body>
</html>