
<?php
/**
 * embed_js.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

// Set content type
header('Content-Type: text/javascript');

// Get post ID
$postId = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($postId)) {
    echo "console.error('No post ID provided');";
    exit();
}

// Sanitize post ID
$postId = htmlspecialchars($postId, ENT_QUOTES, 'UTF-8');

// Include database connection
require_once '../include/config.php';
require_once '../include/db.php';

// Fetch post data
$stmt = $connection->prepare("SELECT * FROM public_post WHERE postid = ? AND viewable = 1");
$stmt->bind_param("s", $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "console.error('Post not found or not public');";
    $stmt->close();
    exit();
}

$post = $result->fetch_assoc();
$stmt->close();

// Update hit counter
$connection->query("UPDATE public_post SET post_hits = post_hits + 1 WHERE postid = '" . 
                  $connection->real_escape_string($postId) . "'");

// Process post data
$postTitle = $post['post_title'];
$postSyntax = $post['post_syntax'];
$postDate = date('Y-m-d H:i', strtotime($post['post_date']));
$postHits = $post['post_hits'];

// Include GeSHi for syntax highlighting
include_once '../include/geshi.php';
$geshi = new GeSHi($post['post_text'], $postSyntax);
$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
$geshi->set_line_style('background: #f8f8f8;', 'background: #f2f2f2;');
$highlightedCode = $geshi->parse_code();

// Escape JavaScript strings
function escapeJS($str) {
    return str_replace(
        array("\\", "'", "\r", "\n", "</script>"),
        array("\\\\", "\\'", "\\r", "\\n", "</' + 'script>"),
        $str
    );
}

$postTitle = escapeJS($postTitle);
$postSyntax = escapeJS($postSyntax);
$highlightedCode = escapeJS($highlightedCode);
$postUrl = escapeJS($config['site_url'] . '/' . $postId);
?>

(function() {
    var phpbinEmbed = function() {
        // Create container
        var container = document.createElement('div');
        container.className = 'phpbin-embed';
        container.style.fontFamily = 'Arial, sans-serif';
        container.style.border = '1px solid #ddd';
        container.style.borderRadius = '4px';
        container.style.margin = '15px 0';
        container.style.overflow = 'hidden';
        
        // Create header
        var header = document.createElement('div');
        header.className = 'phpbin-header';
        header.style.backgroundColor = '#f4f4f4';
        header.style.padding = '8px 15px';
        header.style.borderBottom = '1px solid #ddd';
        header.style.display = 'flex';
        header.style.justifyContent = 'space-between';
        header.style.alignItems = 'center';
        
        // Title and syntax
        var titleDiv = document.createElement('div');
        titleDiv.innerHTML = '<strong>' + '<?php echo $postTitle; ?>' + '</strong>';
        titleDiv.innerHTML += ' <span style="color:#888;font-size:12px;margin-left:10px;"><?php echo $postSyntax; ?></span>';
        
        // Link to original
        var linkDiv = document.createElement('div');
        var link = document.createElement('a');
        link.href = '<?php echo $postUrl; ?>';
        link.target = '_blank';
        link.textContent = 'View Original';
        link.style.fontSize = '12px';
        link.style.color = '#0366d6';
        link.style.textDecoration = 'none';
        linkDiv.appendChild(link);
        
        header.appendChild(titleDiv);
        header.appendChild(linkDiv);
        
        // Create code container
        var codeContainer = document.createElement('div');
        codeContainer.className = 'phpbin-code';
        codeContainer.style.maxHeight = '400px';
        codeContainer.style.overflow = 'auto';
        codeContainer.style.backgroundColor = '#fff';
        codeContainer.style.padding = '10px';
        codeContainer.style.fontSize = '13px';
        codeContainer.innerHTML = '<?php echo $highlightedCode; ?>';
        
        // Create footer
        var footer = document.createElement('div');
        footer.className = 'phpbin-footer';
        footer.style.backgroundColor = '#f4f4f4';
        footer.style.padding = '5px 15px';
        footer.style.borderTop = '1px solid #ddd';
        footer.style.fontSize = '11px';
        footer.style.color = '#888';
        footer.style.textAlign = 'right';
        footer.textContent = 'Powered by PHP-Bin';
        
        // Assemble the widget
        container.appendChild(header);
        container.appendChild(codeContainer);
        container.appendChild(footer);
        
        // Insert the widget at the script tag location
        var scripts = document.getElementsByTagName('script');
        var thisScript = scripts[scripts.length - 1];
        thisScript.parentNode.insertBefore(container, thisScript);
    };
    
    // Run the embed function
    phpbinEmbed();
})();
