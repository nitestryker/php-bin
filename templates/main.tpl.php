<?php
# Php-bin - is a PHP-based web application that allows you to store text online 
# for a set period of time.

# php-bin is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# php-bin is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with php-bin.  If not, see <http://www.gnu.org/licenses/>.
/**
 * @package php-bin
 * @copyright Copyright (C) 2013 - 2015  Jeremy Stevens - jeremiahstevens@gmail.com
 * @copyright Copyright (C) 2013 - 2015  Nitestryker - nitestryker@gmail.com 
 * @copyright Copyight  (c) 2013 - 2015  Nitestryker Software Inc. 
 * @link http://jeremystevens.org
*/

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once 'include/config.php';

// Start session with strict security settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Initialize variables
$form = '';
$uname = '';
$uid = null;
$user = '';

// Handle logged in state
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $uid = $_SESSION['uid'] ?? null;
    $user = $_SESSION['username'] ?? '';
    $uname = $user;
    
    $form = sprintf(
        'Welcome <a href="u/%s">%s</a>&nbsp;<a href="logout.php">logout</a>',
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

require_once 'classes/conn.class.php';

// Handle login submission
if (isset($_POST['login'])) {
    try {
        $cmd = new Conn();
        $cmd->login(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );
        header('Refresh: 1; url=index.php');
        exit();
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
    }
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

    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
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

    //This line is updated to use addEventListener instead of Event.observe
    document.addEventListener('DOMContentLoaded', function() {
        resizeIt(); //initial on load
    });

    //This line is updated to use addEventListener instead of Event.observe
    document.addEventListener('keyup', resizeIt); 

</script>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/"><?=$config['site_name'];?></a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <li><a href="index.php">Add a new paste</a></li>
                    <li><a href="archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="index.php" method="post"/>
                <?=$form;?></form>
                <ul class="nav pull-right">
                 <input type="hidden" name="action" value="sent"> 
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
                <form class="form-search" name="form1" method="get" action="search.php">
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
                        <?php include 'include/live.php';?>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">


            <!--Here-->


            <form method="POST" action="index.php?action=post" name="addpaste" id="addpaste">
                <div class="base-block">
                    <div class="title">Add a new paste</div>
                    <div id="container">
                        <p align="center"><textarea data-placement="top" rel="tooltip"
                                                    data-original-title="Create and share your pastes quickly" rows="16"
                                                    class="span11" name="post_text" id="post_text"
                                                    onkeydown="textAreaAdjust(this)" style="overflow:hidden"></textarea>
                        </p>

                        <div id="textCopy"/>
                    </div>


                    <div class="row-fluid">
                        <div class="base-block span6">
                            <div class="title">Paste Settings</div>
                            <h4>Syntax</h4>

                            <p>
                                <select name="post_syntax" id="select1" class="uniform" data-placement="right"
                                        rel="tooltip" data-original-title="Select your language">
                                    <option value="c" name="c">C</option>
                                    <option value="css" name="css">CSS</option>
                                    <option value="cpp" name="cpp">C++</option>
                                    <option value="html4strict" name="html4strict">HTML (4 Strict)</option>
                                    <option value="java" name="java">Java</option>
                                    <option value="perl" name="perl">Perl</option>
                                    <option value="php" name="php">PHP</option>
                                    <option value="python" name="python">Python</option>
                                    <option value="ruby" name="ruby">Ruby</option>
                                    <option value="text" name="text">Plain Text</option>
                                    <option value="asm" name="asm">ASM (Nasm Syntax)</option>
                                    <option value="xhtml" name="xhtml">XHTML</option>
                                    <option value="actionscript" name="actionscript">Actionscript</option>
                                    <option value="ada" name="ada">ADA</option>
                                    <option value="apache" name="apache">Apache Log</option>
                                    <option value="applescript" name="applescript">AppleScript</option>
                                    <option value="autoit" name="autoit">AutoIT</option>
                                    <option value="bash" name="bash">Bash</option>
                                    <option value="bptzbasic" name="bptzbasic">BptzBasic</option>
                                    <option value="c_mac" name="c_mac">C for Macs</option>
                                    <option value="csharp" name="csharp">C#</option>
                                    <option value="ColdFusion" name="ColdFusion">coldfusion</option>
                                    <option value="delphi" name="delphi">Delphi</option>
                                    <option value="eiffel" name="eiffel">Eiffel</option>
                                    <option value="fortran" name="fortran">Fortran</option>
                                    <option value="freebasic" name="freebasic">FreeBasic</option>
                                    <option value="gml" name="gml">GML</option>
                                    <option value="groovy" name="groovy">Groovy</option>
                                    <option value="inno" name="inno">Inno</option>
                                    <option value="java5" name="java5">Java 5</option>
                                    <option value="javascript" name="javascript">Javascript</option>
                                    <option value="latex" name="latex">LaTeX</option>
                                    <option value="mirc" name="mirc">mIRC</option>
                                    <option value="mysql" name="mysql">MySQL</option>
                                    <option value="nsis" name="nsis">NSIS</option>
                                    <option value="objc" name="objc">Objective C</option>
                                    <option value="ocaml" name="ocaml">OCaml</option>
                                    <option value="oobas" name="oobas">OpenOffice BASIC</option>
                                    <option value="orcale8" name="orcale8">Orcale 8 SQL</option>
                                    <option value="pascal" name="pascal">Pascal</option>
                                    <option value="plsql" name="plsql">PL/SQL</option>
                                    <option value="qbasic" name="qbasic">Q(uick)BASIC</option>
                                    <option value="robots" name="robots">robots.txt</option>
                                    <option value="scheme" name="scheme">Scheme</option>
                                    <option value="sdlbasic" name="sdlbasic">SDLBasic</option>
                                    <option value="smalltalk" name="smalltalk">Smalltalk</option>
                                    <option value="smarty" name="smarty">Smarty</option>
                                    <option value="sql" name="sql">SQL</option>
                                    <option value="tcl" name="tcl">TCL</option>
                                    <option value="vbnet" name="vbnet">VB.NET</option>
                                    <option value="vb" name="vb">Visual BASIC</option>
                                    <option value="winbatch" name="winbatch">Winbatch</option>
                                    <option value="xml" name="xml">XML</option>
                                    <option value="z80" name="z80">z80 ASM</option>
                                    <option value="4cs" name="4cs">gadv 4Cs</option>

                                </select>
                            </p>
                            <h4>Expiration</h4>

                            <p>
                                <select name="post_exp" class="uniform" data-placement="right" rel="tooltip"
                                        data-original-title="Select the life of your paste">
                                    <option value="0">Never</option>
                                    <option value="1">10 Minutes</option>
                                    <option value="2">1 Hour</option>
                                    <option value="3">1 Day</option>
                                    <option value="4">1 Month</option>
                                </select>
                            </p>
                            <h4>Exposure</h4>

                            <p>
                                <?php


                                // check if users is logged in
                                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                                    $option = "<option value='private'>Private</option>";
                                    $uname = $_SESSION['username'];
                                } else {
                                    $option = "";
                                    $uname = "guest";
                                }
                                ?>
                                <select name="exposure" class="uniform" data-placement="right" rel="tooltip"
                                        data-original-title="Set your paste private or public">
                                    <option value="public">Public</option>
                                    <?=$option;?>
                                    <option value="unlisted">Unlisted</option>
                                    <input type="hidden" name="posters_name" value="<?=$uname;?>">
                                </select>
                            </p>
                        </div>
                        <!--/span-->


                        <div class="base-block span6">
                            <div class="title">Paste Name</div>
                            <h4>Paste Name</h4>

                            <p>
                                <input data-placement="right" rel="tooltip" data-original-title="Paste Name"
                                       name="post_title" type="text" value="" class="st-forminput"/><br/>
                            </p>
                            <input name="submit" type="submit" value="Submit" id="submit" class="btn btn-primary"/>
                            </p>
                        </div>
                        <!--/span-->


            </form>
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

</div> <!-- /container -->
</body>
</html>