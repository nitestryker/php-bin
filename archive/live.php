<?php
declare(strict_types=1);

/**
 * Live.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 1.0.8-modern
 */

ini_set('display_errors', '0');
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Status</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha384-H+K7U5CnX" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            function getStatus() {
                $('#status').load('recent.php');
                setTimeout(getStatus, 5000);
            }

            getStatus();
        });
    </script>
    <style>
        /* Optional: basic styling to improve UX */
        body {
            font-family: Arial, sans-serif;
            padding: 1rem;
            background-color: #f9f9f9;
        }

        #status {
            border: 1px solid #ccc;
            padding: 1rem;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div id="status">Loading...</div>
</body>
</html>
