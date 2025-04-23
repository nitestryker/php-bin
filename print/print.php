<?php
declare(strict_types=1);

/**
 * Print Page
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens 
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.0.0
 */

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'');

try {
    require_once '../include/config.php';
    require_once '../include/db.php';
    require_once '../classes/conn.class.php';
    require_once '../include/geshi.php';

    // Initialize database connection
    $conn = new DatabaseConnection($config);

    // Validate and sanitize post ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id <= 0) {
        throw new InvalidArgumentException('Invalid post ID');
    }

    // Prepare and execute query with parameterized statement
    $stmt = $conn->prepare("SELECT * FROM public_post WHERE postid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || !($row = $result->fetch_assoc())) {
        throw new RuntimeException('Post not found');
    }

    // Verify post is viewable
    if ($row['viewable'] !== 1) {
        throw new RuntimeException('This paste is not viewable');
    }

    // Process post data
    $title = htmlspecialchars($row['post_title'] ?: 'Untitled', ENT_QUOTES, 'UTF-8');
    $code = $row['post_text'];
    $syntax = $row['post_syntax'];

    // Initialize GeSHi for syntax highlighting
    $geshi = new GeSHi($code, $syntax);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $highlighted_code = $geshi->parse_code();

} catch (Exception $e) {
    error_log("Print Error: " . $e->getMessage());
    http_response_code(500);
    $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print: <?php echo $title ?? 'Error'; ?></title>
    <style>
        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 13px;
            line-height: 1.5;
            margin: 20px;
            padding: 0;
            background: #ffffff;
            color: #000000;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 12px;
            text-align: center;
        }
        .code-container {
            margin: 20px 0;
            max-width: 100%;
            overflow: auto;
        }
        .print-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }
        .error {
            color: #ff0000;
            padding: 20px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php else: ?>
        <div class="header">
            <h1><?php echo $title; ?></h1>
            <div class="no-print">
                <button onclick="window.print();" class="print-button">Print</button>
                <button onclick="window.close();" class="print-button">Close</button>
            </div>
        </div>
        
        <div class="code-container">
            <?php echo $highlighted_code; ?>
        </div>
        
        <div class="footer">
            <p>Printed from <?php echo htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    <?php endif; ?>
    
    <script>
        // Auto-print when page loads
        window.onload = () => {
            if (!document.querySelector('.error')) {
                setTimeout(() => window.print(), 1000);
            }
        };
    </script>
</body>
</html>
