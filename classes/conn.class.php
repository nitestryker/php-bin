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

class DatabaseConnection
{
    private $connection;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect()
    {
        $this->connection = mysqli_connect(
            $this->config['dbhost'], 
            $this->config['dbusername'], 
            $this->config['dbpasswd'], 
            $this->config['database_name']
        );

        if (!$this->connection) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Set charset to utf8mb4
        mysqli_set_charset($this->connection, 'utf8mb4');
    }

    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql);
        if (!$result) {
            error_log("SQL Error: " . mysqli_error($this->connection));
        }
        return $result;
    }

    public function prepare($sql)
    {
        return mysqli_prepare($this->connection, $sql);
    }

    public function escape($string)
    {
        return mysqli_real_escape_string($this->connection, $string);
    }

    public function fetchAssoc($result)
    {
        return mysqli_fetch_assoc($result);
    }

    public function fetchArray($result)
    {
        return mysqli_fetch_array($result);
    }

    public function numRows($result)
    {
        return mysqli_num_rows($result);
    }

    public function affectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    public function close()
    {
        mysqli_close($this->connection);
    }

    public function lastInsertId()
    {
        return mysqli_insert_id($this->connection);
    }
}
?>