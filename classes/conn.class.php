
<?php
/**
 * conn.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
require 'include/config.php';
require 'classes/main.class.php';
$host = $dbhost;
$username = $dbusername;
$password = $dbpasswd;
$b = $database_name;


// database connection class 
class Conn
{
    private $db_host = ""; // server name
    private $db_user = ""; // user name
    private $db_pass = ""; // password
    private $db_dbname = ""; // database name
    private $db_charset = ""; // optional character set (i.e. utf8)
    private $pdo = null; // PDO connection
    private $active_row = -1; // current row
    private $error_desc = ""; // last error string
    public $ThrowExceptions = false;

    /*
      constructor
     */
    public function __construct($connect = true, $database = null, $server = null,
                                $username = null, $password = null, $charset = null)
    {
        if ($database !== null) $this->db_dbname = $database;
        if ($server !== null) $this->db_host = $server;
        if ($username !== null) $this->db_user = $username;
        if ($password !== null) $this->db_pass = $password;
        if ($charset !== null) $this->db_charset = $charset;

        if (strlen($this->db_host) > 0 &&
            strlen($this->db_user) > 0
        ) {
            if ($connect) $this->Open();
        }
    }

    /*
      Open Connection to Database
     */
    public function Open($database = null, $server = null, $username = null,
                         $password = null, $charset = null, $pcon = false)
    {
        $this->ResetError();

        // use pre-set values
        if ($database !== null) $this->db_dbname = $database;
        if ($server !== null) $this->db_host = $server;
        if ($username !== null) $this->db_user = $username;
        if ($password !== null) $this->db_pass = $password;
        if ($charset !== null) $this->db_charset = $charset;

        $this->active_row = -1;

        try {
            // Set charset part of DSN if provided
            $charset_part = ($this->db_charset) ? ";charset={$this->db_charset}" : "";
            
            // Create PDO connection
            $dsn = "mysql:host={$this->db_host};dbname={$this->db_dbname}{$charset_part}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Add persistent connection if needed
            if ($pcon) {
                $options[PDO::ATTR_PERSISTENT] = true;
            }
            
            $this->pdo = new PDO($dsn, $this->db_user, $this->db_pass, $options);
            return true;
        } catch (PDOException $e) {
            $this->SetError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    //  Selects database & charset
    public function SelectDatabase($database, $charset = "")
    {
        $this->ResetError();
        if (!$charset) $charset = $this->db_charset;
        
        try {
            // For PDO, we need to create a new connection with the new database
            $this->db_dbname = $database;
            $dsn = "mysql:host={$this->db_host};dbname={$database}";
            
            // Set charset if provided
            if (strlen($charset) > 0) {
                $dsn .= ";charset={$charset}";
            }
            
            $this->pdo = new PDO($dsn, $this->db_user, $this->db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            return true;
        } catch (PDOException $e) {
            $this->SetError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    // db is connected
    public function IsConnected()
    {
        return ($this->pdo !== null);
    }

    // Kill Message
    public function Kill($message = "")
    {
        if (strlen($message) > 0) {
            exit($message);
        } else {
            exit($this->Error());
        }
    }

    // on error display error message + number
    public function Error()
    {
        $error = $this->error_desc;
        if (empty($error)) {
            if ($this->error_number != 0) {
                $error = "Unknown Error (#" . $this->error_number . ")";
            } else {
                $error = false;
            }
        } else {
            if ($this->error_number > 0) {
                $error .= " (#" . $this->error_number . ")";
            }
        }
        return $error;
    }

    // set error message & number
    private function SetError($errorMessage = "", $errorNumber = 0)
    {
        try {
            if (strlen($errorMessage) > 0) {
                $this->error_desc = $errorMessage;
            } else {
                $this->error_desc = ($this->pdo) ? $this->pdo->errorInfo()[2] : "Unknown error";
            }
            
            if ($errorNumber != 0) {
                $this->error_number = $errorNumber;
            } else {
                $this->error_number = ($this->pdo) ? $this->pdo->errorInfo()[1] : 0;
            }
        } catch (Exception $e) {
            $this->error_desc = $e->getMessage();
            $this->error_number = -999;
        }
        
        if ($this->ThrowExceptions) {
            if (isset($this->error_desc) && $this->error_desc != NULL) {
                throw new Exception($this->error_desc . ' (' . __LINE__ . ')');
            }
        }
    }

    /**
     * Resets the error data
     */
    private function ResetError()
    {
        $this->error_desc = '';
        $this->error_number = 0;
    }

    /**
     * Check the users login credentials
     */
    public function login($username = "", $pass = "")
    {
        // make connection to the Db
        include 'include/config.php';

        //variables from config
        $dbhost = $dbhost;
        $uname = $dbusername;
        $password = $dbpasswd;
        $database_name = $database_name;

        // open connection to db
        $this->Open($database_name, $dbhost, $uname, $password);

        // if username or password are blank show error!
        if ((!$username) || (!$pass)) {
            // display error if fields are blank;
            echo "<div id='wb_Shape3' style='position:absolute;left:902px;top:47px;width:582px;height:48px;z-index:36;padding:0;'><font color='Red'>Please enter your Username and Password!</font> <br /></div>";
            $this->redirect2();
            return false;
        }

        try {
            // Convert password to md5 hash
            $pass_hash = md5($pass);
            
            // Use prepared statement to prevent SQL injection
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->execute([$username, $pass_hash]);
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                
                $uid = $row['uid'];
                $username = $row['username'];
                $email = $row['email'];
                $_SESSION['loggedin'] = true;
                $_SESSION['uid'] = $uid;
                $_SESSION['username'] = $username;
                $_SESSION['verify'] = $username;
                $this->rd();
                return true;
            } else {
                // display error if login is incorrect
                echo "<div id='wb_Shape3' style='position:absolute;left:727px;top:59px;width:582px;height:48px;z-index:36;padding:0;'><font color=red><p>You could not be logged in! Either the username or password are incorrect</p><br /></font></div>";
                $this->redirect2();
                return false;
            }
        } catch (PDOException $e) {
            $this->SetError($e->getMessage(), $e->getCode());
            return false;
        }
    }
    
    public function rd()
    {
        $location = $_SERVER['HTTP_REFERER'];
        header("Location: $location");
    }
    
    // redirect if needed 
    function redirect2()
    {
        // include config file get site folder index
        include 'include/config.php';
        $index = $config['site_index'];
        header("refresh: 6; url=/$index/index.php");
    }
    
    //successful login 
    function successful()
    {
        // include config file get site folder index
        include 'include/config.php';
        $index = $config['site_index'];
        header("refresh:1; url=/$index/index.php");
    }

    /**
     * Execute query and return array of results
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->SetError($e->getMessage(), $e->getCode());
            return false;
        }
    }
    
    /**
     * Execute query that doesn't return results (INSERT, UPDATE, DELETE)
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->SetError($e->getMessage(), $e->getCode());
            return false;
        }
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

} // end of class 
?>
