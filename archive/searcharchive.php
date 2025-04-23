<?php
declare(strict_types=1);

/**
 * searcharchive.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 1.0.8-modern
 */

require_once '../include/error_handler.php';
require_once '../include/config.php';
require_once '../include/db.php';
require_once '../include/session.php';

$time_start = microtime(true);

// Set error reporting
if ($error_logging === 1) {
    set_error_handler('error_handler');
}
error_reporting($display_errors === 1 ? E_ALL : 0);

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Auth state
$form = '';
if (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $user = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
    $form  = "Welcome <a href='../u/{$user}'>{$user}</a>&nbsp;";
    $form .= "<a href='logout.php'>logout</a>";
} else {
    $form = <<<HTML
<input type='hidden' name='login'>
<input class='span2' type='text' name='username' placeholder='User name'>
<input class='span2' type='password' name='password' placeholder='Password'>
<input type='submit' name='submit' value='Login' class='btn'/>
<ul class='nav pull-right'>
    <li><a href='register.php'>Registration</a></li>
</ul>
HTML;
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    require_once '../classes/conn.class.php';
    $cmd = new Conn();
    $cmd->login($_POST['username'] ?? '', $_POST['password'] ?? '');
    header('Refresh:1; url=' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="rien">
    <meta name="keywords" content="rien"/>
    <meta name="author" content="Php-pastebin">

    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="js/bootstrap.js"></script>
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
            <a class="brand" href="/"><?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li><a href="../index.php">Add a new paste</a></li>
                    <li><a href="../archive.php">View all pastes</a></li>
                </ul>

                <!-- Login Form -->
                <form class="navbar-form pull-right" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <?= $form ?>
                </form>
            </div>
        </div>
    </div>
</div>

<header class="jumbotron masthead" id="overview"></header>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2 offset1">
            <div class="base-block">
                <div class="title">Paste Search</div>
                <form class="form-search" name="form1" method="get" action="../search.php">
                    <input type="text" name="term" class="span12" />
                </form>
            </div>

            <div class="base-block">
                <div class="title">Recent pastes</div>
                <ul class="nav nav-list">
                    <li><?php include 'live.php'; ?></li>
                </ul>
            </div>
        </div>

        <div class="span8">
            <div class="base-block">
                <div class="title"><?= htmlspecialchars($_GET['syntax'] ?? 'Search Results', ENT_QUOTES, 'UTF-8') ?></div>

                <img src="../img/code.jpg" height="36" width="48" alt="code icon">
                <font size="6">Paste::Syntax: <b><?= htmlspecialchars($_GET['syntax'] ?? '', ENT_QUOTES, 'UTF-8') ?></b></font>

                <div class="c" style="font-family: monospace;"><br><br>
                    <table width="100%" border="0">
                        <thead>
                            <tr>
                                <th>Name / Title</th>
                                <th>Posted</th>
                                <th>Total Hits</th>
                                <th>Syntax</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                require_once '../classes/search.class.php';
                                $search = new searcher();
                                $search->searchbysyntax();
                                if (!empty($search->error)) {
                                    echo "<tr><td colspan='4'>" . htmlspecialchars($search->error, ENT_QUOTES, 'UTF-8') . "</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="span8"></footer>
</div>

<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
    });
</script>

</body>
</html>
