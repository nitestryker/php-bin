<?php
declare(strict_types=1);

/**
 * Database Connection Handler
 * 
 * @package PHP-Bin
 * @version 2.1.0
 */

class Database {
    private static ?mysqli $connection = null;
    private static array $config;

    public static function init(array $config): void {
        self::$config = $config;
    }

    public static function getConnection(): mysqli {
        if (self::$connection === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                self::$connection = new mysqli(
                    self::$config['dbhost'],
                    self::$config['dbusername'],
                    self::$config['dbpasswd'],
                    self::$config['database_name']
                );

                self::$connection->set_charset('utf8mb4');
            } catch (mysqli_sql_exception $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new RuntimeException('Database connection failed');
            }
        }

        return self::$connection;
    }

    public static function query(string $query): mysqli_result|bool {
        return self::getConnection()->query($query);
    }

    public static function prepare(string $query): mysqli_stmt {
        return self::getConnection()->prepare($query);
    }

    public static function escape(string $string): string {
        return self::getConnection()->real_escape_string($string);
    }

    public static function close(): void {
        if (self::$connection instanceof mysqli) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}

// Initialize with config
require_once __DIR__ . '/config.php';
Database::init($config);

// Register shutdown function
register_shutdown_function([Database::class, 'close']);