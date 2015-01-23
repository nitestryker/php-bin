<?php
error_reporting(E_ALL);
$post_title = clean($_POST['name']);
$post_syntax = clean($_POST['syntax']);
$post_exp = clean($_POST['expi']);
$exp_int = clean($_POST['expi']);
$exposure = clean($_POST['expo']);
$post_text = $_POST['text'];
include '../include/config.php';
// make connection to database 
$connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
                or die ("Couldn't connect to server.");
            $db = mysql_select_db("$database_name", $connection)
                or die("Couldn't select database.");

// generate a random post id 
$post_id = generateRandomString(8);
echo $post_id;
// get the server URL and index 
$server = $config['site_url'];
$index = $config['site_index'];

// create link to be shortened
         include '../include/config.php';
        $link = $config['site_url']; 
                  $link .= "/";  
                  $link .= $config['site_index'];
                  $link .="/$post_id";

// set variable for shorten link
        include '../include/config.php';
          $buser = $config['bitly_username'];
          $bkey = $config['bitly_api'];
        $tlink = shortLink($link,$buser,$bkey,$format='txt');
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

 // let's store the users ip address
        $users_ip = get_ip();

// if the post is viewable set it to 1 
        $viewable = $exposure;
         if ($viewable == "Public")
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
            $post_title = $_POST['name'];
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
// MySQL query
        $sql = mysql_query("INSERT INTO public_post
    		(postid,posters_name,ip,post_title,post_syntax,exp_int,post_exp,post_text,post_date,post_size,post_hits,bitly,viewable) VALUES('$post_id', '$posters_name','$users_ip','$post_title','$post_syntax','$exp_int','$post_exp','$post_text','$post_date','$post_size','$post_hits','$slink','$viewable') ")
            or die(mysql_error());

        if (!$sql) {
            echo 'There has been an error. Please contact the webmaster.';
        } else {
        }










/*
* functions 
*
*/
//generate random id  
function generateRandomString($length = 10)
    {
        $characters = "1234567890";
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
// clean the vars 
function clean($var = null)
    {
        // sanitation
        $var = htmlspecialchars($var);
        $var = trim(htmlspecialchars($var, ENT_QUOTES, "utf-8"));
        $var = strip_tags($var);
        return $var;
    }
function shortLink($url,$login,$appkey,$format='txt') 
  {
   //include config for bit.ly credentials
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	$results = curl_get_result($connectURL);
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

?>