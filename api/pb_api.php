<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../include/config.php';

// Input sanitization
function clean(?string $var): string {
    return htmlspecialchars(trim(strip_tags($var ?? '')), ENT_QUOTES, 'UTF-8');
}

// Generate random post ID
function generateRandomString(int $length = 10): string {
    $characters = '1234567890';
    return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
}

// Shorten link via Bit.ly
function shortLink(string $url, string $login, string $appkey, string $format = 'txt'): string {
    $connectURL = "https://api-ssl.bitly.com/v3/shorten?login={$login}&apiKey={$appkey}&uri=" . urlencode($url) . "&format={$format}";
    return curl_get_result($connectURL);
}

// Simple curl wrapper
function curl_get_result(string $url): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data ?: '';
}

// IP detection
function get_ip(): string {
    $headers = function_exists('apache_request_headers') ? apache_request_headers() : $_SERVER;

    return filter_var(
        $headers['X-Forwarded-For'] ?? $headers['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
        FILTER_VALIDATE_IP,
        FILTER_FLAG_IPV4
    ) ?: '0.0.0.0';
}

// Collect and clean input
$post_title = clean($_POST['name'] ?? '');
$post_syntax = clean($_POST['syntax'] ?? '');
$post_exp = clean($_POST['expi'] ?? '');
$exp_int = clean($_POST['expi'] ?? '');
$exposure = clean($_POST['expo'] ?? '');
$post_text = $_POST['text'] ?? '';

if (empty($post_text)) {
    exit('Please enter some text.');
}

if ($post_title === '') {
    $post_title = 'untitled';
}

// Connect to DB
$mysqli = new mysqli($dbhost, $dbusername, $dbpasswd, $database_name);
if ($mysqli->connect_error) {
    error_log("DB connection error: " . $mysqli->connect_error);
    exit("Couldn't connect to server.");
}

// Config
$post_id = generateRandomString(8);
$server = $config['site_url'];
$index = $config['site_index'];
$link = "{$server}/{$index}/{$post_id}";

// Bitly short link
$slink = shortLink($link, $config['bitly_username'], $config['bitly_api']);
if (in_array($slink, ['RATE_LIMIT_EXCEEDED', 'INVALID_URI', 'MISSING_ARG_ACCESS_TOKEN', 'MISSING_ARG_LOGIN', 'UNKNOWN_ERROR'], true)) {
    $slink = '';
}

$users_ip = get_ip();
$viewable = strtolower($exposure) === 'public' ? 1 : 0;
$posters_name = 'guest';

date_default_timezone_set('America/Los_Angeles');
$post_date = (new DateTime())->format('Y-m-d H:i:s');

// Expiration logic
$post_exp_time = match ((int)$post_exp) {
    1 => '+10 minutes',
    2 => '+1 hour',
    3 => '+1 day',
    4 => '+1 month',
    default => '+1 month',
};

$post_exp = (new DateTime())->modify($post_exp_time)->format('Y-m-d H:i:s');

// Calculate size in KB
$post_size = number_format(strlen($post_text) / 1024, 2);
$post_hits = null;

// Use prepared statement to insert
$stmt = $mysqli->prepare(
    "INSERT INTO public_post 
    (postid, posters_name, ip, post_title, post_syntax, exp_int, post_exp, post_text, post_date, post_size, post_hits, bitly, viewable) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log("SQL Prepare Error: " . $mysqli->error);
    exit("An internal error occurred.");
}

$stmt->bind_param(
    'ssssssssssssi',
    $post_id,
    $posters_name,
    $users_ip,
    $post_title,
    $post_syntax,
    $exp_int,
    $post_exp,
    $post_text,
    $post_date,
    $post_size,
    $post_hits,
    $slink,
    $viewable
);

if (!$stmt->execute()) {
    error_log("SQL Execute Error: " . $stmt->error);
    exit('There has been an error. Please contact the webmaster.');
}

$stmt->close();
$mysqli->close();

echo $post_id;
