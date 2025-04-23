<?php
declare(strict_types=1);

/**
 * Profile template
 * @package PHP-Bin
 * @version 2.0.0
 */

require_once '../../include/config.php';

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Initialize variables with type safety
$profile = $_GET['usr'] ?? '';
$form = '';
$uid = null;
$usr = '';
$uname = '';

// Handle logged in state
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $uid = $_SESSION['uid'] ?? null;
    $usr = $_SESSION['username'] ?? '';
    $uname = $usr;

    $form = sprintf(
        'Welcome <a href="../%s">%s</a>&nbsp;<a href="../../index.php?action=logout">logout</a>',
        htmlspecialchars($usr, ENT_QUOTES),
        htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES)
    );
} else {
    $form = <<<HTML
        <input class="span2" type="text" name="username" placeholder="User name">
        <input class="span2" type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Login" class="btn"/>
        </form>
        <ul class="nav pull-right">
            <li><a href="../../register.php">Registration</a></li>
HTML;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($config['site_name'] ?? '') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Profile">
    <meta name="author" content="PHP-Bin">

    <link href="../../css/style.css" rel="stylesheet">
    <link href="../../css/bootstrap.css" rel="stylesheet">
    <link href="../../css/bootstrap-responsive.css" rel="stylesheet">
    <link href="http://cdn.jotfor.ms/static/formCss.css?3.1.1174" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="http://cdn.jotfor.ms/css/styles/nova.css?3.1.1174" />
    <link type="text/css" media="print" rel="stylesheet" href="http://cdn.jotfor.ms/css/printForm.css?3.1.1174" />
    <style type="text/css">
        .form-label{
            width:150px !important;
        }
        .form-label-left{
            width:150px !important;
        }
        .form-line{
            padding-top:12px;
            padding-bottom:12px;
        }
        .form-label-right{
            width:150px !important;
        }
        body, html{
            margin:0;
            padding:0;
            background:false;
        }

        .form-all{
            margin:0px auto;
            padding-top:20px;
            width:650px;
            color:#555 !important;
            font-family:'Lucida Grande';
            font-size:14px;
        }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
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
            <a class="brand" href="/">PHP-Bin</a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <li><a href="../../index.php">Add a new paste</a></li>
                    <li><a href="../../archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="../../index.php?action=login" method="post"/>
                <?= $form; ?></form>
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
                <form class="form-search" name="form1" method="post" action="../../search.php">
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
                            <?php include 'live.php'; ?>
                        </div>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">
            <div class="base-block">
                <div class="title">User Profile</div>
                <div class="profile-content">
                    <img src="../../include/avatar.php?uimage=<?= htmlspecialchars($profile) ?>" 
                         alt="Profile Avatar" height="80" width="80">
                    <h3><?= htmlspecialchars($profile) ?>'s Profile</h3>
                    <!-- Profile content here -->
                </div>
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
<script src="../../js/jquery-1.4.4.min.js"></script>
<script src="../../js/bootstrap.js"></script>
</body>
</html>