<?php
declare(strict_types=1);

/**
 * Error Handler
 *
 * @package PHP-Bin
 * @version 2.1.0
 */

if (!session_id()) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

$error = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'An unknown error occurred';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>
<body>
    <div class="container">
        <div class="alert alert-danger mt-4">
            <h4 class="alert-heading">Error</h4>
            <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <hr>
            <p class="mb-0">
                <a href="javascript:history.back()" class="btn btn-outline-danger">Go Back</a>
            </p>
        </div>
    </div>
</body>
</html>