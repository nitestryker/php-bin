<?php
/**
 * reg.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
 ob_start();
class reg
{

    // show the registration form;
    function showform()
    {
        include 'templates/registration.tpl.php';
    }


    // create users account
    function regUser()
    {
        // sql injection protection
        $username = mysql_real_escape_string($_POST['username']);
        $password = mysql_real_escape_string($_POST['password']);
        $email = mysql_real_escape_string($_POST['email']);

        //check if email address is valid
        if ($this->isValidEmail($email)) {
            // if email is vaild do nothing
        }
        else {
            // if email is invalid show registration form again and throw error
            echo "<div id='wb_Shape3' style='position:absolute;left:609px;top:177px;width:582px;height:48px;z-index:36;padding:0;'><font color=red>Please enter a vaild email address:</font> <br />";
            unset($email);
            $this->regError(); // Show the form again!
            exit(); // exit the script so that we do not create this account
        }

        // XSS protection
        $username = strip_tags($username);
        $password = strip_tags($password);
        $email = strip_tags($email);

        /* Let's do some checking and ensure that the user's email address or username
          does not exist in the database */
        $sql_email_check = mysql_query("SELECT email FROM users WHERE email='$email'");

        $email_check = mysql_num_rows($sql_email_check);

        if (($email_check > 0)) {
            echo "<div id='wb_Shape3' style='position:absolute;left:809px;top:57px;width:582px;height:48px;z-index:36;padding:0;'><font color=red>Please fix the following errors:</font> <br />";
            if ($email_check > 0) {
               echo "<div id='wb_Shape3' style='position:absolute;left:00px;top:37px;width:582px;height:48px;z-index:36;padding:0;'><font color=red>Your email address has already been used by another member in our database. Please submit a different Email address!<br /></font> <br />";
 

              unset($email_address);
            }
            // show the form again.
             $this->regError();       
             // exit the script so that we do not create this account    
             exit();
  
        }
        $uid = $this->generateRandomString(9);

        // set the userid for session
        $_SESSION['u'] = $uid;
        $_SESSION['uid'] = $uid;

        /*
             Encrypt the password insert data into database
            */
        $db_password = md5($password);
        $sql = mysql_query("INSERT INTO users (uid,username,password,email,join_date)
      			VALUES('$uid','$username', '$db_password','$email',now())") or die (mysql_error());
        if (!$sql) {
            echo "<div id='wb_Shape3' style='position:absolute;left:209px;top:57px;width:582px;height:48px;z-index:36;padding:0;'>There has been an error creating your account. Please contact the webmaster.</div>";
        } else {
            $userid = mysql_insert_id();
            session_start();

            //store some variables in session
            $_SESSION['userid'] = $userid;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;

            // generate users post table
            require_once ('include/gen.php');
            echo "<div id='wb_Shape3' style='position:absolute;left:650px;top:57px;width:582px;height:48px;z-index:36;padding:0;'><font color='red'></i>Your account has been created you may now login</font></i></div>";


            /*
                  *
                  * Send users a registration email here
                  *
                  *
                  */
            $this->redirect();

        }

    } // end of regUser Function


    function redirect()
    {
        header('refresh: 6; url=index.php');

         /* removed this line */
       // include 'index.php';
    }
    
 function showagain()
    {
        header('refresh: 2; url=register.php');

    }

    // generate random userid;
    function generateRandomString($length = 9)
    {
        $characters = "1234567890";
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;

    }

//Check-Function
    function isValidEmail($email)
    {
        //Perform a basic syntax-Check
        //If this check fails, there's no need to continue
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        //extract host
        list($user, $host) = explode("@", $email);
        //check, if host is accessible
        if (!checkdnsrr($host, "MX") && !checkdnsrr($host, "A")) {
            return false;
        }

        return true;
    }

    function regError()
    {
        header('refresh:4; url=register.php');
    }


}

// end of class
?>