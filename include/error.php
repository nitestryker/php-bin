<?php
/**
 * error.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Get error code
$errorCode = isset($_GET['code']) ? intval($_GET['code']) : 404;

// Define error messages
$errorMessages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable'
];

// Get error message or default to "Unknown Error"
$errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'Unknown Error';

// Set the HTTP response code
http_response_code($errorCode);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error <?php echo $errorCode; ?></title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .error-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #d9534f;
        }
        .error-image {
            margin: 20px 0;
        }
        .home-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Error <?php echo $errorCode; ?></h1>
        <p><?php echo $errorMessage; ?></p>
        <div class="error-image">
            <img src="img/sad.png" alt="Error" width="128">
        </div>
        <div class="home-link">
            <a href="index.php">Return to Home</a>
        </div>
    </div>
</body>
</html>