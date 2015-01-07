<?php
/**
 * profile.tpl.php (user profile template)
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
require_once '../../include/config.php';
$profile = $_GET['usr'];
// if user is not logged in show login form at the top
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $uid = $_SESSION['uid'];
    $usr = $_SESSION['username'];
    $form = "Welcome <a href='../$usr'>" . $_SESSION['username'] . "</a>&nbsp;";
    $form .= "<a href='../../index.php?action=logout'>logout</a>";
    $uname = $_SESSION['username'];
} else {
    $form = "<input class='span2' type='text' name='username' placeholder='User name'>";
    $form .= "<input class='span2' type='password' name='password' placeholder='Password'>";
    $form .= "<input type='submit' name='submit' value='Login' class='btn'/>";
    $form .= "</form>";
    $form .= "<ul class='nav pull-right'>";
    $form .= "<li><a href='../../register.php'>Registration</a></li>";
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
            <a class="brand" href="/">Pastebin Clone</a>

            <div class="nav-collapse collapse">

                <ul class="nav">
                    <li><a href="../../index.php">Add a new paste</a></li>
                    <li><a href="../../archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="../../index.php?action=login" method="post"/>
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
                            <?include 'live.php';?>
                        </div>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">
            <div class="base-block">
             <div class="title">edit profile</div>
                <img src="../../include/avatar.php?uimage=<?=$profile;?>" height="80" width="80">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <i><b><font size="5"><?=$username;?>'s bin</font></b></i>
                   <div class="edit"
                     style='position:absolute;left:1140px;top:10px;width:582px;height:48px;z-index:36;padding:0;'><?=$back;?></div>
                <div class="info"style='position:absolute;left:121px;top:105px;width:582px;height:48px;z-index:36;padding:0;'>
                </div>
                <!--posted code goes here-->
                 <form class="jotform-form" action="submit.php" enctype="multipart/form-data" method="post" name="form_32046876133151" id="32046876133151" accept-charset="utf-8">
  <input type="hidden" name="formID" value="32046876133151" />
  <div class="form-all">
    <ul class="form-section">
      <li class="form-line" id="id_2">
        <label class="form-label-left" id="label_2" for="input_2"> Upload Avatar </label>
        <div id="cid_2" class="form-input">
           <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
          <input class="form-upload" type="file" id="input_2" name="q2_uploadAvatar">
        </div>
      </li>
      <li class="form-line" id="id_3">
        <label class="form-label-left" id="label_3" for="input_3"> Location: </label>
        <div id="cid_3" class="form-input">
          <input type="text" class=" form-textbox" data-type="input-textbox" id="input_3" name="q3_location" size="20" value="<?=$location;?>" />
        </div>
      </li>
      <li class="form-line" id="id_4">
        <label class="form-label-left" id="label_4" for="input_4"> Website </label>
        <div id="cid_4" class="form-input">
          <input type="text" class=" form-textbox" data-type="input-textbox" id="input_4" name="q4_website" size="20" value="<?=$website;?>"/>
        </div>
      </li>
      <li class="form-line" id="id_1">
        <div id="cid_1" class="form-input-wide">
          <div style="margin-left:156px" class="form-buttons-wrapper">
            <button id="input_1" type="submit" class="form-submit-button">
              Submit
            </button>
          </div>
        </div>
      </li>
      <li style="display:none">
        Should be Empty:
        <input type="text" name="website" value="" />
      </li>
    </ul>
  </div>
  <input type="hidden" id="simple_spc" name="simple_spc" value="32046876133151" />
  <script type="text/javascript">
  document.getElementById("si" + "mple" + "_spc").value = "32046876133151-32046876133151";
  </script>
</form>

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
