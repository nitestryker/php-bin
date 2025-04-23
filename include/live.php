<?php
/**
 * Live.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script type="text/javascript">
        $(function() {
            getStatus();
        });

        function getStatus() {
            $('#status').load('recent.php');
            setTimeout(getStatus, 5000);
        }
    </script>
</head>
<body>
<div id="status"></div>
</body>
</html>
