<?php
/*
+------------------------------------------------
|    live.php (jquery to pull recent post)
|   =============================================
|    by Nitestryker
|   (c) 2013 Nitestryker Software
|   http://nitestryker.net
|   =============================================
|   git: https://github.com/nitestryker/phpbin.git
|   Licence Info: GPL
+------------------------------------------------
*/

?>
<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

    <script type="text/javascript">

        $(function () {

            getStatus();

        });

        function getStatus() {

            $('div#status').load('recent.php');
            setTimeout("getStatus()", 5000);

        }

    </script>

</head>
<body>
<div id="status"></div>
</body>