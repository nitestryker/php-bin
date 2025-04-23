<?php
declare(strict_types=1);

/**
 * Error page for user editpost directory
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

require_once '../../include/config.php';
require_once '../../include/db.php';
require_once '../../include/session.php';
require_once '../../classes/conn.class.php';

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Sanitize user input
$usr = htmlspecialchars($_GET['usr'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=htmlspecialchars($config['site_name'] ?? 'PHP-Bin', ENT_QUOTES);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PHP Bin Error Page">
    <meta name="keywords" content="pastebin, code sharing, error"/>
    <meta name="author" content="PHP-Bin">

    <link href="../../css/style.css" rel="stylesheet">
    <link href="../../css/bootstrap.css" rel="stylesheet">
    <link href="../../css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.js"></script>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="span12">
            <div class="alert alert-error">
                <h2>Error</h2>
                <p>Sorry, an error occurred while processing your request.</p>
                <p>User: <?=htmlspecialchars($usr, ENT_QUOTES);?></p>
                <p><a href="../../index.php" class="btn btn-primary">Return to Homepage</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>