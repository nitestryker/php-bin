
<?php
/**
 * Error Handler
 *
 * @package PHP-Bin
 * @version 2.0.0
 */

declare(strict_types=1);

class ErrorHandler {
    private const LOG_PATH = __DIR__ . '/../logs/';
    
    public static function init(): void {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
        
        // Ensure log directory exists
        if (!is_dir(self::LOG_PATH)) {
            mkdir(self::LOG_PATH, 0755, true);
        }
    }
    
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $message = self::formatError($errno, $errstr, $errfile, $errline);
        self::logError($message, self::getLogFile($errno));
        
        if (self::isFatal($errno)) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        
        return true;
    }
    
    public static function handleException(Throwable $exception): void {
        $message = self::formatException($exception);
        self::logError($message, 'php-exception.log');
        
        if (self::isDevMode()) {
            self::displayDevError($exception);
        } else {
            self::displayProductionError();
        }
    }
    
    public static function handleFatalError(): void {
        $error = error_get_last();
        
        if ($error !== null && self::isFatal($error['type'])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
    
    private static function formatError(int $errno, string $errstr, string $errfile, int $errline): string {
        $type = self::getErrorType($errno);
        return sprintf(
            "[%s] %s: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $type,
            $errstr,
            $errfile,
            $errline
        );
    }
    
    private static function formatException(Throwable $exception): string {
        return sprintf(
            "[%s] Uncaught %s: %s in %s on line %d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }
    
    private static function logError(string $message, string $logFile): void {
        error_log($message, 3, self::LOG_PATH . $logFile);
    }
    
    private static function getLogFile(int $errno): string {
        return match($errno) {
            E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR => 'php-error.log',
            E_WARNING, E_USER_WARNING => 'php-warning.log',
            E_NOTICE, E_USER_NOTICE => 'php-notice.log',
            default => 'php-other.log',
        };
    }
    
    private static function getErrorType(int $errno): string {
        return match($errno) {
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
            default => 'Unknown Error',
        };
    }
    
    private static function isFatal(int $errno): bool {
        return in_array($errno, [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR
        ]);
    }
    
    private static function isDevMode(): bool {
        return defined('ENVIRONMENT') && ENVIRONMENT === 'development';
    }
    
    private static function displayDevError(Throwable $exception): void {
        http_response_code(500);
        echo '<div style="background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;">';
        echo '<h3>' . get_class($exception) . '</h3>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $exception->getLine() . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    
    private static function displayProductionError(): void {
        http_response_code(500);
        echo '<div style="background-color: #ffdfdf; color: #990000; border: 1px solid #990000; padding: 10px;">';
        echo '<h3>Application Error</h3>';
        echo '<p>An unexpected error occurred. The site administrator has been notified.</p>';
        echo '</div>';
    }
}

// Initialize error handler
ErrorHandler::init();
