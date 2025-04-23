<?php
/**
 * Database Connection Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

declare(strict_types=1);

class DatabaseConnection
{
    private ?mysqli $connection = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect(): void
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $this->connection = new mysqli(
                $this->config['dbhost'],
                $this->config['dbusername'],
                $this->config['dbpasswd'],
                $this->config['database_name']
            );

            $this->connection->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function query(string $sql): mysqli_result|bool
    {
        try {
            return $this->connection->query($sql);
        } catch (mysqli_sql_exception $e) {
            error_log("SQL Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function prepare(string $sql): mysqli_stmt
    {
        return $this->connection->prepare($sql);
    }

    public function escape(string $string): string
    {
        return $this->connection->real_escape_string($string);
    }

    public function fetchAssoc(mysqli_result $result): ?array
    {
        return $result->fetch_assoc();
    }

    public function fetchArray(mysqli_result $result): array|null|false
    {
        return $result->fetch_array();
    }

    public function numRows(mysqli_result $result): int
    {
        return $result->num_rows;
    }

    public function affectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    public function lastInsertId(): int
    {
        return $this->connection->insert_id;
    }

    public function close(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
