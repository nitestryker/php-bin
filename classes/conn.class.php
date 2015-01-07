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
    private $db_pcon = false; // use persistent connection?
    private $active_row = -1; // current row
    private $error_desc = ""; // last mysql error string
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
        if (is_bool($pcon)) $this->db_pcon = $pcon;

        $this->active_row = -1;

        // Open persistent or normal connection
        if ($pcon) {
            $this->mysql_link = @mysql_pconnect(
                $this->db_host, $this->db_user, $this->db_pass);
        } else {
            $this->mysql_link = @mysql_connect(
                $this->db_host, $this->db_user, $this->db_pass);
        }
        // Connect to mysql server failed?
        if (!$this->IsConnected()) {
            $this->SetError();
            return false;
        } else {
            // Select a database (if specified)
            if (strlen($this->db_dbname) > 0) {
                if (strlen($this->db_charset) == 0) {
                    if (!$this->SelectDatabase($this->db_dbname)) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    if (!$this->SelectDatabase(
                        $this->db_dbname, $this->db_charset)
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
    }

    //  Selects database & charset
    public function SelectDatabase($database, $charset = "")
    {
        $return_value = true;
        if (!$charset) $charset = $this->db_charset;
        $this->ResetError();
        if (!(mysql_select_db($database))) {
            $this->SetError();
            $return_value = false;
        } else {
            if ((strlen($charset) > 0)) {
                if (!(mysql_query("SET CHARACTER SET '{$charset}'", $this->mysql_link))) {
                    $this->SetError();
                    $return_value = false;
                }
            }
        }
        return $return_value;
    }

    // db is connected
    public function IsConnected()
    {
        if (gettype($this->mysql_link) == "resource") {
            return true;
        } else {
            return false;
        }
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
            if ($this->error_number <> 0) {
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
                if ($this->IsConnected()) {
                    $this->error_desc = mysql_error($this->mysql_link);
                } else {
                    $this->error_desc = mysql_error();
                }
            }
            if ($errorNumber <> 0) {
                $this->error_number = $errorNumber;
            } else {
                if ($this->IsConnected()) {
                    $this->error_number = @mysql_errno($this->mysql_link);
                } else {
                    $this->error_number = @mysql_errno();
                }
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
     *
     */
    private function ResetError()
    {
        $this->error_desc = '';
        $this->error_number = 0;
    }


    /**
     * check the users login credentials
     *
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

        // sanitize users input
        $username = mysql_real_escape_string($username);
        $pass = mysql_real_escape_string($pass);

        // if usename or password are blank show error!
        if ((!$username) || (!$pass)) {
            // display error if fields are blank;
            echo "<div id='wb_Shape3' style='position:absolute;left:902px;top:47px;width:582px;height:48px;z-index:36;padding:0;'><font color='Red'>Please enter your Username and Password!</font> <br /></div>";
            $this->redirect2();
        }

        // Convert password to md5 hash
        $pass = md5($pass);

        // check if the user info validates the db
        $sql = mysql_query("SELECT * FROM users WHERE username='$username' AND password='$pass'");
        $login_check = mysql_num_rows($sql);
        if ($login_check > 0) {
            while ($row = mysql_fetch_array($sql)) {
                foreach ($row AS $key => $val) {
                    $$key = stripslashes($val);
                }
                $uid = $row['uid'];
                $username = $row['username'];
                $email = $row['email'];
                $_SESSION['loggedin'] = true;
                $_SESSION['uid'] = $uid;
                $_SESSION['username'] = $username;
                $_SESSION['verify'] = $username;
                $this->rd();
            } 

        } else {

            // display error if login is incorrect
            echo "<div id='wb_Shape3' style='position:absolute;left:727px;top:59px;width:582px;height:48px;z-index:36;padding:0;'><font color=red><p>You could not be logged in! Either the username or password are incorrect</p><br /></font></div>";
            $this->redirect2();
        }


    } // end of login function
      public function rd()
         {
            $location = $_SERVER['HTTP_REFERER'];
                header("Location: $location");
          }
// redirect	if needed 
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

} // end of class 

?>
