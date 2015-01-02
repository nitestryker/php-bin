<?php
/*
+------------------------------------------------
|    post.tpl.php ( paste template)
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $uid = $_SESSION['uid'];
    $user = $_SESSION['username'];
    $form = "Welcome <a href='u/$user'>" . $_SESSION['username'] . "</a>&nbsp;";
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
include_once 'include/config.php';
include_once 'classes/conn.class.php';
include_once 'include/cronjob.php';
if (isset($_POST['submit'])) {
    $cmd = new Conn();
    $cmd->login($_POST['username'], $_POST['password']);
    $location = $_SERVER['HTTP_REFERER'];
 header('Refresh:1; url=$location');
}
$pid = $_GET['pid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=$config['site_name'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=$config['site_name'];?>">
    <meta name="keywords" content="<?=$config['site_name'];?>"/>

    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="ZeroClipboard.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
        $(document).ready(function () {
            $("#load_recent").load('include/live.php').fadeIn("fast);
        }, 0); // refresh every 10000 milliseconds
    </script>
</head>

<body>

<
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

                <ul class="nav"><??>
                    <li><a href="index.php">Add a new paste</a></li>
                    <li><a href="archive.php">View all pastes</a></li>

                </ul>
                <!---- login form here---->
                <form class="navbar-form pull-right" action="<?=$_SERVER[’PHP_SELF’];?>" method="post"/>
                <?=$form;?></form>
                <ul class="nav pull-right">

                    <!----Registration button here-->


                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
<div>
    <Br>
</div>
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
                        <!----live post here--->
                        <div class="load_recent">
                            <?include 'include/live.php'?>
                        </div>
                    </li>
                </ul>
            </div>
            <!--/base-block-->

        </div>

        <div class="span8">
            <div class="base-block">
                 </script>
                 <script language='JavaScript' src='../js/sfm_moveable_popup.js' type='text/javascript'>
                 <script type="text/javascript" src="js/ZeroClipboard.js"></script>
                 <script language="JavaScript">
            ZeroClipboard.setMoviePath('js/ZeroClipboard.swf');
        $(document).ready(function(){
              $(".clickme").each(function (i) {
                    var clip = new ZeroClipboard.Client();

                    var myTextToCopy = $(this).val();
                    clip.setText( myTextToCopy );
                        clip.addEventListener('complete', function (client, text) {
                 alert("Copied text to clipboard." );
                });
                    clip.glue( $(this).attr("id") );



              });


        });

    </script>
  <script type="text/javascript">
function doSomething() {
     var id =<?=$pid?>;
     $.get('report.php?pid='+id+'');
    alert("Thank you, the post was reported to the site administrator!"); 
    location.reload();
    return false;
}
</script>

                <div class="title">#<?=$post_id;?></div>
                <img src="<?=$imagesrc;?>" height=50 width=50>
                &nbsp;&nbsp;<img src="img/notepad.png" height=30 width=30>
                <i><b><?=$post_title;?></b></i>
                <!--posted code goes here-->
                <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                By: <?=$namelink;?> on <?=$fdate;?> | Syntax: <a
                href="archive/<?=$post_syntax;?>"><?=$post_syntax;?></a> | Size: <?=$post_size?> KB |
                Hits: <?=$post_hits?>  | Expires: <?=$expires;?> |<Br>
                <div id="options" style="position:relative;left:86px;top:21%;width:100%;"><a href="raw/<?=$post_id;?>" target="popupwindow">Raw Code</a> |<?=$link_title;?>&nbsp;<?=$bitly;?>| <a href="download.php?pid=<?=$post_id?>" target="popupwindow"> Download </a> | <a href="print/<?=$post_id?>" target="popupwindow"> Print </a> | &nbsp;<a href="#" onclick="doSomething(<?=$post_id?>);">Report Abuse</a></div>
    <script language="JavaScript"> 
        var clip = new ZeroClipboard.Client(),  
            myTextToCopy = '<?=$bitly?>';                    
        clip.glue('d_clip_button');
        clip.addEventListener('onMouseOver', clipboardEvent);
        function clipboardEvent() {
            clip.setText( myTextToCopy );
        }
    </script>
             <br>
                <?php
                  $share = $config['site_url']; 
                  $share .= "/";  
                  $share .= $config['site_index'];
                  $share .="/$post_id";
                   ?>                                                                                              
                <a href="https://www.facebook.com/sharer.php?u=<?=$share;?>" target="popupwindow" style="position:absolute;left:90px;top:55;"><img src="img/fb.gif"> </a>  <a href='https://twitter.com/share?url=<?=$share;?>' target="popupwindow" style="position:absolute;left:155px;top:20;"><img src="img/twitter2.png" width='57' height='1'></a>

                </script>
                <div class="c" style="font-family: monospace;">
                <?php
                if (isset($error)) {
                    echo "an error has occured";
                    exit();
                }
                ?>
                <br>
                <?php include_once 'include/geshi.php';
                $source = $post_text;
                $syntax = $post_syntax;
                // Simply echo the highlighted code
                $geshi = new GeSHi($source, $syntax);
                $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 1);
                echo $geshi->parse_code();
                ?>
                    </SPAN>
                    </div>
                </div>
                </div>



            </div>
            <!-- /container -->
</body>
</html>


