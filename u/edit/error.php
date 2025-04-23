<?php
declare(strict_types=1);

/**
 * error.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

header('HTTP/1.1 404 Not Found');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error 404 - Page Not Found</title>
</head>
<body>
    <h1>Error 404</h1>
    <p>The requested profile could not be found.</p>
    <p><a href="../../index.php">Return to homepage</a></p>
</body>
</html>