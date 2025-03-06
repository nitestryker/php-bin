
<?php
/**
 * Database Connection File
 * 
 * @package PHP-Bin
 * @version 2.0.0
 */

// Include configuration
require_once 'config.php';

// Create database connection using MySQLi
$connection = mysqli_connect($dbhost, $dbusername, $dbpasswd, $database_name);

// Check connection
if (mysqli_connect_errno()) {
    $error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
    
    // Log error
    error_log($error_message);
    
    // Display error message if display_errors is enabled
    if ($display_errors) {
        die($error_message);
    } else {
        die("Database connection error. Please check the error logs or contact the administrator.");
    }
}

// Set charset
mysqli_set_charset($connection, "utf8mb4");

// Common database functions
function db_query($query) {
    global $connection;
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        error_log("Database query error: " . mysqli_error($connection));
    }
    
    return $result;
}

function db_fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}

function db_fetch_array($result) {
    return mysqli_fetch_array($result);
}

function db_num_rows($result) {
    return mysqli_num_rows($result);
}

function db_affected_rows() {
    global $connection;
    return mysqli_affected_rows($connection);
}

function db_insert_id() {
    global $connection;
    return mysqli_insert_id($connection);
}

function db_escape($string) {
    global $connection;
    return mysqli_real_escape_string($connection, $string);
}

function db_close() {
    global $connection;
    mysqli_close($connection);
}

// Prepared statement functions
function db_prepare($query) {
    global $connection;
    return mysqli_prepare($connection, $query);
}

function db_stmt_bind_param($stmt, $types, ...$params) {
    return mysqli_stmt_bind_param($stmt, $types, ...$params);
}

function db_stmt_execute($stmt) {
    return mysqli_stmt_execute($stmt);
}

function db_stmt_get_result($stmt) {
    return mysqli_stmt_get_result($stmt);
}

function db_stmt_close($stmt) {
    return mysqli_stmt_close($stmt);
}
?>
