
<?php
/**
 * Error Handler
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

/**
 * Custom error handler function
 * 
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool True to prevent PHP default error handler
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Get current error reporting level
    $error_reporting = error_reporting();
    
    // If error reporting is turned off or error not included in error_reporting
    if ($error_reporting === 0 || !($errno & $error_reporting)) {
        return true;
    }
    
    // Different handling based on error type
    switch ($errno) {
        case E_USER_ERROR:
            $error_type = 'Fatal Error';
            $log_file = 'php-error.log';
            break;
        case E_USER_WARNING:
        case E_WARNING:
            $error_type = 'Warning';
            $log_file = 'php-warning.log';
            break;
        case E_USER_NOTICE:
        case E_NOTICE:
            $error_type = 'Notice';
            $log_file = 'php-notice.log';
            break;
        default:
            $error_type = 'Unknown Error';
            $log_file = 'php-unknown.log';
    }
    
    // Format error message for logging
    $message = date('[Y-m-d H:i:s]') . " $error_type: $errstr in $errfile on line $errline" . PHP_EOL;
    
    // Determine if we should log to file
    $log_to_file = true;
    
    // If we're in development environment, display error on screen
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo "<div style='background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;'>";
        echo "<h3>$error_type</h3>";
        echo "<p><strong>Message:</strong> $errstr</p>";
        echo "<p><strong>File:</strong> $errfile</p>";
        echo "<p><strong>Line:</strong> $errline</p>";
        echo "</div>";
    }
    
    // Log error to file
    if ($log_to_file) {
        $log_path = __DIR__ . '/../logs/' . $log_file;
        error_log($message, 3, $log_path);
    }
    
    // Fatal errors should halt execution
    if ($errno === E_USER_ERROR) {
        exit(1);
    }
    
    // Return true to prevent default PHP error handler
    return true;
}

// Register custom error handler
set_error_handler('customErrorHandler');

/**
 * Custom exception handler
 * 
 * @param Exception|Throwable $exception The exception
 */
function customExceptionHandler($exception) {
    $errfile = $exception->getFile();
    $errline = $exception->getLine();
    $errstr = $exception->getMessage();
    $trace = $exception->getTraceAsString();
    
    // Format exception message for logging
    $message = date('[Y-m-d H:i:s]') . " Uncaught Exception: $errstr in $errfile on line $errline" . PHP_EOL;
    $message .= "Stack trace:" . PHP_EOL . $trace . PHP_EOL . PHP_EOL;
    
    // Determine if we should log to file
    $log_to_file = true;
    
    // If we're in development environment, display exception on screen
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo "<div style='background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;'>";
        echo "<h3>Uncaught Exception</h3>";
        echo "<p><strong>Message:</strong> $errstr</p>";
        echo "<p><strong>File:</strong> $errfile</p>";
        echo "<p><strong>Line:</strong> $errline</p>";
        echo "<p><strong>Stack trace:</strong></p>";
        echo "<pre>$trace</pre>";
        echo "</div>";
    } else {
        // In production, show a friendly error message
        echo "<div style='background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;'>";
        echo "<h3>Application Error</h3>";
        echo "<p>An unexpected error occurred. The site administrator has been notified.</p>";
        echo "</div>";
    }
    
    // Log exception to file
    if ($log_to_file) {
        $log_path = __DIR__ . '/../logs/php-exception.log';
        error_log($message, 3, $log_path);
    }
    
    // Exit with an error code
    exit(1);
}

// Register custom exception handler
set_exception_handler('customExceptionHandler');

/**
 * Handle fatal errors
 */
function fatalErrorHandler() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $errno = $error['type'];
        $errstr = $error['message'];
        $errfile = $error['file'];
        $errline = $error['line'];
        
        // Format fatal error message for logging
        $message = date('[Y-m-d H:i:s]') . " Fatal Error: $errstr in $errfile on line $errline" . PHP_EOL;
        
        // Determine if we should log to file
        $log_to_file = true;
        
        // If we're in development environment, display error on screen
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo "<div style='background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;'>";
            echo "<h3>Fatal Error</h3>";
            echo "<p><strong>Message:</strong> $errstr</p>";
            echo "<p><strong>File:</strong> $errfile</p>";
            echo "<p><strong>Line:</strong> $errline</p>";
            echo "</div>";
        } else {
            // In production, show a friendly error message
            echo "<div style='background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;'>";
            echo "<h3>Application Error</h3>";
            echo "<p>An unexpected error occurred. The site administrator has been notified.</p>";
            echo "</div>";
        }
        
        // Log error to file
        if ($log_to_file) {
            $log_path = __DIR__ . '/../logs/php-fatal.log';
            error_log($message, 3, $log_path);
        }
    }
}

// Register shutdown function to catch fatal errors
register_shutdown_function('fatalErrorHandler');
?>
