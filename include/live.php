<?php
/**
 * live.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
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

            $('div#status').load('include/recent.php');
            setTimeout("getStatus()", 5000);

        }

    </script>

</head>
<body>
<div id="status"></div>
</body>