<?php
/**
 * post.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */

class post
{


    function getPost($postid = null)
    {
        // if an error occurs
        $action = (isset($_GET['action'])) ? $_GET['action'] : "null";
        if ($action == "error") {
            include 'include/error.php';
        }
        // sanitation
        $postid = htmlspecialchars($postid);
        $postid = trim(htmlspecialchars($postid, ENT_QUOTES, "utf-8"));
        $postid = mysql_real_escape_string($postid);
        $_SESSION['tpid'] = $postid;

        if ($postid == "") {
            header('refresh:0; url=index.php');
        }

        $sql = "SELECT * FROM public_post WHERE postid = $postid";
        $result = mysql_query($sql);
       // prevent mysql_fetch_array warning 
       if($result === FALSE) { 
         exit(); // TODO: better error handling
       }
        while ($row = mysql_fetch_array($result)) {
            $id = $row['public_postid'];
            $_SESSION['id'] = $id;
            $this->id = $id;
            $post_id = $row['postid'];
            $this->post_id = $post_id;
            $posters_name = $row['posters_name'];
            $this->posters_name = $posters_name;
            $post_title = $row['post_title'];
            $this->post_title = $post_title;
            $post_syntax = $row['post_syntax'];
            $this->post_syntax = $post_syntax;
            $exp_int = $row['exp_int'];
            $this->exp_int = $exp_int;
            $post_exp = $row['post_exp'];
            $this->post_exp = $post_exp;
            $post_text = $row['post_text'];
            $this->post_text = $post_text;
            $post_date = $row['post_date'];
            $this->post_date = $post_date;
            $post_size = $row['post_size'];
            $this->post_size = $post_size;
            $post_hits = $row['post_hits'];
            $this->post_hits = $post_hits;
            $bitly = $row['bitly'];
            $this->bitly = $bitly;
            $_SESSION['sphits'] = $post_hits;
            $pdate = $post_date;
            $post_hits = $post_hits + 1;
            $this->hits = $post_hits;
            $_SESSION['id'] = $id;
            $_SESSION['hits'] = $post_hits;
            $_SESSION['pid'] = $post_id;
            $_SESSION['pname'] = $posters_name;
            $pid = $post_id;
            $fdate = date('F j, Y', strtotime($post_date));

            // get the posters id and put it in a session
            $sql = "SELECT * FROM users WHERE username= '$posters_name'";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_array($result)) {
                $t4 = $row['uid'];
                $_SESSION['t4'] = $t4;
            }

        }
        // if there is not data for that post number throw error
        if (empty($post_id)) {
            include 'include/error.php';
            exit();
        } else {

        }

        if ($posters_name != "guest") {
            $namelink = "<a href='u/$posters_name'>$posters_name</a>";
            $this->namelink = $namelink;
            /*
            * todo
           * include_once 'include/userhits.php';
            */
           $_SESSION['reguser'] = "1";
        } else {
            $namelink = "A Guest";
            $this->namelink = $namelink;
        }

        /* todo
         add hits
        */

    } // end of function

    // hits function
    function hits()
    {
        include 'include/config.php';

        $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // get variable sessions
        $post_hits = $_SESSION['hits'];
        $hc = $post_hits;
        $pid = $_SESSION['pid'];
        $_SESSION['hc'] = $hc;

        $id = $_SESSION['id'];


        $sql = "UPDATE public_post
         SET post_hits=$hc
         WHERE public_postid=$id";

        $retval = mysql_query( $sql, $connection );
        if(! $retval )
        {
            die('Could not update data: ' . mysql_error());
        }
        mysql_close($connection);
    } // end of function



   // update total accumulated hits for all post
    function totalHits()
    {
        include 'include/config.php';

        $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // get variable sessions
        $post_hits = $_SESSION['hits'];
        $hc = $post_hits;
        $pid = $_SESSION['pid'];
        $pname = $_SESSION['pname'];

        $id = $_SESSION['id'];


        $sql = "UPDATE users
         SET total_hits=$hc
         WHERE username='$pname'";
        $retval = mysql_query( $sql, $connection );
        if(! $retval )
        {
            die('Could not update data: ' . mysql_error());
        }
        mysql_close($connection);
    } // end of function

    function updateUsrhits($uid)
    {
        $uid = $uid;
        include 'include/config.php';

        $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // get variable sessions
        $post_hits = $_SESSION['hits'];
        $hc = $_SESSION['hc'];
        $pid = $_SESSION['pid'];
        $pname = $_SESSION['pname'];

        $id = $_SESSION['id'];


        $sql = "UPDATE userp_$uid
         SET post_hits=$hc
         WHERE postid='$pid'";
        $retval = mysql_query( $sql, $connection );
        if(! $retval )
        {
            die('Could not update data: ' . mysql_error());
        }
        mysql_close($connection);
    } // end of function



   // get the userid # from the username
   function getuid($pos)
   {
       $pos = $pos;
 include 'include/config.php';
 $connection = mysql_connect("$dbhost","$dbusername","$dbpasswd")
 or die ("Couldn't connect to server.");
 $db = mysql_select_db("$database_name", $connection)
 or die("Couldn't select database.");

       $sql = "SELECT * FROM users WHERE username ='$pos'";
       $result = mysql_query($sql);
       while ($row = mysql_fetch_array($result))
       {
           $posname = $row['uid'];
           return $posname;
          $this->posname = $posname;
       }
   }



    // logout user
    function logout()
    {
        $post_id = $_SESSION['tpid'];
        // Unset all of the session variables.
        $_SESSION = array();
        // Finally, destroy the session.
        session_destroy();
        // redirect users to front page
        header("Location: /$post_id");
    }


    // check if the users is logged in
    function logincheck()
    {
        // if user is logged give them V.I.P
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $this->results = "user";
            return $this->results;
            $_SESSION['exposure'] = $_POST['exposure'];
        } else {

            $this->results = "guest";
            return $this->results;
        }


    } // end of logincheck


    // random string generator
    function generateRandomString($length = 10)
    {
        $characters = "1234567890";
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    // shorten links with bit.ly
   function shortLink($url,$login,$appkey,$format='txt') 
  {
   //include config for bit.ly credentials
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	$results = $this->curl_get_result($connectURL);
  return $results;
  }
  // return the results 
  function curl_get_result($url) 
  {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
  }

    // post by registered users
    function RegUser()
    {
		// edited here on 01.28.14
        include '../include/config.php';

        // get server info from config file;
        $server = $config['site_url'];
        $index = $config['site_index'];


        // get userid from session
        $uid = $_SESSION['uid'];

        // genereate a random post id
        $post_id = $this->generateRandomString(8);

        // create link to be shortened
         include 'include/config.php';
         $link = $config['site_url']; 
                  $link .= "/";  
                  $link .= $config['site_index'];
                  $link .="/$post_id";

        // set variable for shorten link
          include 'include/config.php';
          $buser = $config['bitly_username'];
          $bkey = $config['bitly_api'];
        $slink = $this->shortLink($link,$buser,$bkey,$format='txt');
         
         // prevernt bit.ly api failure messages in db
        switch ($slink) 
         // TODO: log these to a file for the administrator to see. 
        {
        case "RATE_LIMIT_EXCEEDED":
           $slink = " ";
           break;
        case "INVALID_URI":
         $slink = "";
          break;
        case "MISSING_ARG_ACCESS_TOKEN":
         $slink = "";
          break;
        case "MISSING_ARG_LOGIN":
         $slink = "";
          break;
        case "UNKNOWN_ERROR":
        $slink = ""; 
           break;
      default:
        $slink = $slink;
        }
        // save the post id to a session
        $_SESSION['postid'] = $post_id;
        $_SESSION['slink'] = $slink;

        // form variables
        $post_text = $_POST['post_text'];
        $post_text = mysql_real_escape_string($post_text);
        $post_syntax = $_POST['post_syntax'];
        $exp_int = $_POST['post_exp'];
        $post_exp = $_POST['post_exp'];
        $post_title = $_POST['post_title'];
        
        // if post text is blank exit
         if (empty($post_text))
          {
    echo 'please enter some text';
       exit();           
      }

        // if post tile is blank label it untitled
        if ($post_title == "") {
            $post_title = "untitled";
        } else {
            $post_title = $_POST['post_title'];
        }
        
         // calculate expiration date 
          switch($post_exp) {
             // 10 mins
            case 1:
             $date = new DateTime();
             $date->modify("+10 minutes"); 
              $date = $date->format('Y-m-d H:i:s');
              $post_exp = $date;
             break;
             // 1 hour 
            case 2:
               $date = new DateTime();
             $date->modify("+1 hour"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
             break;
             // 24 hours 
            case 3:
             $date = new DateTime();
             $date->modify("+1 day"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
              // 1 month 
            case 4:
               $date = new DateTime();
             $date->modify("+1 month"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
                }
        $posters_name = $_SESSION['username'];
        date_default_timezone_set('America/Los_Angeles');
        $post_date = date('Y-m-d H:i:s');
        $post_size = serialize($post_text);
        $post_size = strlen($post_text) / 1024;
        $post_size = number_format($post_size);
        $post_hits = null;
        $expose = $_POST['exposure'];


        /*
        * if the post is private do not include in public bin
        */

        switch ($expose) {
            case "private":


                //set viewable to false;
                $viewable = "0";
                
                // let's store the users ip address
               $users_ip = $this->get_ip();


                //insert into personal table
                $sql = mysql_query("INSERT INTO  userp_$uid
	 				(postid,posters_name,ip,post_title,post_syntax,post_exp,post_text,post_date,post_size,post_hits,viewable,bitly) VALUES('$post_id', '$posters_name','$users_ip','$post_title','$post_syntax','$post_exp','$post_text','$post_date','$post_size','$post_hits', '$viewable','$slink') ")
                    or die(mysql_error());

                if (!$sql) {
                    echo 'There has been an error. Please contact the webmaster.';
                } else {
                    header("Location: $post_id");
                }
                break;

            case "public":

                // set viewable to true
                $viewable = "1";

                // put the viewable var in a session
                $_SESSION['viewable'] = $viewable;
                
               // let's store the users ip address
               $users_ip = $this->get_ip();

                //insert into personal table
                $sql = mysql_query("INSERT INTO  userp_$uid
	 				(postid,posters_name,ip,post_title,post_syntax,post_exp,post_text,post_date,post_size,post_hits,viewable) VALUES('$post_id', '$posters_name','$users_ip','$post_title','$post_syntax','$post_exp','$post_text','$post_date','$post_size','$post_hits', '$viewable') ")
                    or die(mysql_error());

                if (!$sql) {
                    echo 'There has been an error. Please contact the webmaster.';
                } else {
                    $this->PublicBin();
                    $this->redirect();
                }

                break;
        }


    }

    // if the post is made public put in public bin
    function PublicBin()

    {

        // genereate a random post id
        $post_id = $_SESSION['postid'];

        // shorten link for a public post by registered user
        $slink = $_SESSION['slink'];

         // prevernt bit.ly api failure messages in db
        switch ($slink) 
         // TODO: log these to a file for the administrator to see. 
        {
        case "RATE_LIMIT_EXCEEDED":
           $slink = " ";
           break;
        case "INVALID_URI":
         $slink = "";
          break;
        case "MISSING_ARG_ACCESS_TOKEN":
         $slink = "";
          break;
        case "MISSING_ARG_LOGIN":
         $slink = "";
          break;
        case "UNKNOWN_ERROR":
        $slink = ""; 
           break;
      default:
        $slink = $slink;
        }

        // save the post id to a session
        $_SESSION['postid'] = $post_id;

        // form variables
        $post_text = $_POST['post_text'];
        $post_text = mysql_real_escape_string($post_text);
        $post_syntax = $_POST['post_syntax'];
        $post_exp = $_POST['post_exp'];
        $exp_int = $_POST['post_exp'];
       $post_title = $_POST['post_title'];
        $posters_name = $_POST['posters_name'];

       // if the post is viewable set it to 1 
        $viewable = $_POST['exposure'];
         if ($viewable == "public")
         {
         $viewable = 1;
         } else {
         $viewable = 0;
          }
        // if the title is blank call it untitled
        if ($post_title == "") {
            $post_title = "untitled";
        } else {
            $post_title = $_POST['post_title'];
        } 
       // calculate expiration date 
          switch($post_exp) {
             // 10 mins
            case 1:
             $date = new DateTime();
             $date->modify("+10 minutes"); 
              $date = $date->format('Y-m-d H:i:s');
              $post_exp = $date;
             break;
             // 1 hour 
            case 2:
               $date = new DateTime();
             $date->modify("+1 hour"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
             break;
             // 24 hours 
            case 3:
             $date = new DateTime();
             $date->modify("+1 day"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
              // 1 month 
            case 4:
               $date = new DateTime();
             $date->modify("+1 month"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
                }
        date_default_timezone_set('America/Los_Angeles');
        $post_date = date('Y-m-d H:i:s');
        $post_size = serialize($post_text);
        $post_size = strlen($post_text) / 1024;
        $post_size = number_format($post_size);
        $post_hits = null;
        
         // let's store the users ip address
         $users_ip = $this->get_ip();
        // var_dump($_POST); //(debugging only)


        // MySQL query
        $sql = mysql_query("INSERT INTO public_post
					(postid,posters_name,ip,post_title,post_syntax,exp_int,post_exp,post_text,post_date,post_size,post_hits,bitly,viewable) VALUES('$post_id', '$posters_name','$users_ip','$post_title','$post_syntax','$exp_int','$post_exp','$post_text','$post_date','$post_size','$post_hits','$slink', '$viewable') ")
            or die(mysql_error());

        if (!$sql) {
            echo 'There has been an error. Please contact the webmaster.';
        } else {
        }
    }

           // Redirect User
       public function redirect() {
        include '../include/config.php';
        $post_id = $_SESSION['postid'];
          header("location: $post_id");
        }


    // post by guest
    function Guest()
    {
        // get userid from session

        $server = $config['site_url'];
        $index = $config['site_index'];

        // genereate a random post id
        $post_id = $this->generateRandomString(8);

        // create link to be shortened
         include 'include/config.php';
        $link = $config['site_url']; 
                  $link .= "/";  
                  $link .= $config['site_index'];
                  $link .="/$post_id";

        // set variable for shorten link
        include 'include/config.php';
          $buser = $config['bitly_username'];
          $bkey = $config['bitly_api'];
        $tlink = $this->shortLink($link,$buser,$bkey,$format='txt');
        $slink = $tlink;
        // prevernt bit.ly api failure messages in db
        switch ($slink) 
         // TODO: log these to a file for the administrator to see. 
        {
        case "RATE_LIMIT_EXCEEDED":
           $slink = " ";
           break;
        case "INVALID_URI":
         $slink = "";
          break;
        case "MISSING_ARG_ACCESS_TOKEN":
         $slink = "";
          break;
        case "MISSING_ARG_LOGIN":
         $slink = "";
          break;
        case "UNKNOWN_ERROR":
        $slink = ""; 
           break;
      default:
        $slink = $slink;
        }
        $_SESSION['shortlink'] = $slink;

        // save the post id to a session
        $_SESSION['postid'] = $post_id;

        // form variables
        $post_text = mysql_real_escape_string($_POST['post_text']);
        $post_syntax = $_POST['post_syntax'];
        $post_exp = $_POST['post_exp'];
        $exp_int = $_POST['post_exp'];

        $post_title = $_POST['post_title'];

         // let's store the users ip address
        $users_ip = $this->get_ip();

        // if the post is viewable set it to 1 
        $viewable = $_POST['exposure'];
         if ($viewable == "public")
         {
         $viewable = 1;
         } else {
         $viewable = 0;
          }
       
         // if post text is blank exit
         if (empty($post_text))
          {
    echo 'please enter some text';
       exit();           
      }
        // if the title is blank call it untitled
        if ($post_title == "") {
            $post_title = "untitled";
        } else {
            $post_title = $_POST['post_title'];
        }
           
        // unregistered users are posted as guest
        $posters_name = "guest";
          // calculate expiration date 
          switch($post_exp) {
             // 10 mins
            case 1:
             $date = new DateTime();
             $date->modify("+10 minutes"); 
              $date = $date->format('Y-m-d H:i:s');
              $post_exp = $date;
             break;
             // 1 hour 
            case 2:
               $date = new DateTime();
             $date->modify("+1 hour"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
             break;
             // 24 hours 
            case 3:
             $date = new DateTime();
             $date->modify("+1 day"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
              // 1 month 
            case 4:
               $date = new DateTime();
             $date->modify("+1 month"); 
             $date = $date->format('Y-m-d H:i:s');
             $post_exp = $date;
              break;
                }
        date_default_timezone_set('America/Los_Angeles');
        $post_date = date('Y-m-d H:i:s');
        $post_size = serialize($post_text);
        $post_size = strlen($post_text) / 1024;
        $post_size = number_format($post_size);
        $post_hits = null;

        // var_dump($_POST); // debugging


        // MySQL query
        $sql = mysql_query("INSERT INTO public_post
    		(postid,posters_name,ip,post_title,post_syntax,exp_int,post_exp,post_text,post_date,post_size,post_hits,bitly,viewable) VALUES('$post_id', '$posters_name','$users_ip','$post_title','$post_syntax','$exp_int','$post_exp','$post_text','$post_date','$post_size','$post_hits','$slink','$viewable') ")
            or die(mysql_error());

        if (!$sql) {
            echo 'There has been an error. Please contact the webmaster.';
        } else {
        }
       
    } // end of function

         // get the users ip address 
        function get_ip() {

		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {

			$headers = apache_request_headers();

		} else {

			$headers = $_SERVER;

		}

		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

			$the_ip = $headers['X-Forwarded-For'];

		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {

			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];

		} else {
			
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

		}

		return $the_ip;

	}
} // end of class


?>
