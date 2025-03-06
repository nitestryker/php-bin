
<?php
/**
 * Print Page
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Include required files
require_once '../include/config.php';
require_once '../include/db.php';
require_once '../include/session.php';
require_once '../classes/conn.class.php';
require_once '../include/geshi.php';

// Initialize connection
$conn = new Conn($mysqli, $config);

// Validate the post ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('Invalid post ID');
}

// Get the post data
$stmt = $conn->db->prepare("SELECT * FROM public_post WHERE post_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // Check if post is viewable
    if ($row['viewable'] != 1) {
        die('This paste is not viewable');
    }
    
    $title = !empty($row['post_title']) ? htmlspecialchars($row['post_title'], ENT_QUOTES, 'UTF-8') : 'Untitled';
    $code = $row['post_text'];
    $syntax = $row['post_syntax'];
    
    // Set up GeSHi for syntax highlighting
    $geshi = new GeSHi($code, $syntax);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $highlighted_code = $geshi->parse_code();
} else {
    die('Post not found');
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print: <?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
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
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
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
        <p>Printed from <?php echo $config['site_name']; ?> - <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>
