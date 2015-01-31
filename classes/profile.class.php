<?php
/**
 * profile.class.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
 */
class profile
{

    // constructor
    function __construct($username = null)
    {
        $this->uname = $username;
        $this->getprofile();
    }

    //get profile info from db
    function getprofile()
    {
        $sql = "SELECT * FROM users WHERE username = '$this->uname'";
        $result = mysql_query($sql);
        while ($row = mysql_fetch_array($result)) {

            $this->profileid = $row['uid'];
            $this->theuserid = $this->profileid;
            $this->username = $row['username'];
            $_SESSION['verify2'] = $this->username;
            $this->email = $row['email'];
            $this->website = $row['website'];
            $this->location = $row['location'];
            $this->avatar = $row['avatar'];
            $this->jdate = $row['join_date'];
            $this->my_time = strtotime($this->jdate);
            $this->join_date = $this->time_since($this->my_time);

            $this->tpaste = $row['total_paste'];
        }

    } // end of function


    function time_since($original)
    {
        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
        );

        $today = time(); /* Current unix time  */
        $since = $today - $original;

        // $j saves performing the count function each time around the loop
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {

            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0) {
                // DEBUG print "<!-- It's $name -->\n";
                break;
            }
        }


        $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";


        // if ($i + 1 < $j) {
        // now getting the second item
        //  $seconds2 = $chunks[$i + 1][0];
        //$name2 = $chunks[$i + 1][1];

        // add second item if it's greater than 0
        // if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
        //  $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        //  }
        //  }

        return $print;
    } // end of function


    function userspost($id)
    {
        $profileid = $id;

        // match the sessions
        $verify = $_SESSION['verify'];
        $verify2 = $_SESSION['username'];

        // if the session matches the users profile show them all post including hidden ones
        if ($verify = $verify2) {
            $sql = "SELECT * FROM userp_$profileid";
            $veri = true;
        } else {
            $sql = "SELECT * FROM userp_$profileid WHERE viewable = '1'";
            $veri = false;
        }
        include '../include/config.php';

        $connection = mysql_connect("$dbhost", "$dbusername", "$dbpasswd")
            or die ("Couldn't connect to server.");
        $db = mysql_select_db("$database_name", $connection)
            or die("Couldn't select database.");

        // sql query selected above based on if that is the logged in user
        $sql = $sql;
        $result = mysql_query($sql);
        while ($row = mysql_fetch_array($result)) {
            $getpid = $row['postid'];
            $posters_name = $row['posters_name'];
            $gettitle = $row['post_title'];
            $my_time = strtotime($row['post_date']);
            $postdate = $this->time_since($my_time);
            $getexp = $row['exp_int'];
            $viewable = $row['viewable'];

            // verifies that the user is the profile owner and shows visibility icons or if not shows standard icons
            if ($veri == true) {
                if ($viewable == "1") {
                    $img = "<img src='../img/unlocked.png'>";
                    $style = null;
                } else {
                    $img = "<img src='../img/locked.png' height=14 width=14>";
                    $style = "style='color:red; background: #ffc;text-decoration:underline;'";
                }
            } else {
                $img = "<img src='http://icons.iconarchive.com/icons/semlabs/web-blog/48/post-remove-icon.png' height='18' width='18'>";
            }
            // switch on expire code
            switch ($getexp) {

                case "0":
                    $expires = "never";
                    break;

                case "1":
                    $expires = "10 mins";
                    break;
                case "2":
                    $expires = "1 hour";
                    break;
                case "3":
                    $expires = "1 day";
                    break;
                case "4":
                    $expires = "1 month";
            }
            // get hit count if its zero then hits = 0
            $gethits = $row['post_hits'];
            if ($gethits == "") {
                $gethits = "0";
            } else {
                $gethits = $gethits;
            }
           
            // if user is the page owner allow them to edit paste.
            if ($verify == $posters_name){
              $editpost = "<a href='$posters_name&action=editpost&postid=$getpid'  title='Edit Paste' style='background-color:#FFFFFF;color:#000000;text-decoration:none'><img src='../img/edit.png' height='20' width='20'></a>";
              $delpost = "<a href='$posters_name&action=delpost&postid=$getpid'  title='Delete Paste' style='background-color:#FFFFFF;color:#000000;text-decoration:none'><img src='../img/del.png' height='15' width='15'></a>";
              } else { 
              $editpost = "";
             }  
            $getsyntax = $row['post_syntax'];
            include 'include/config.php';
            $folder = $config['site_index'];
            echo "<td align='justify'>$img &nbsp; <a href='/$folder/$getpid'$style;>$gettitle</a>&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; $editpost &nbsp;  $delpost<hr></td>";
            echo "<td align='justify'>&nbsp;&nbsp;&nbsp;$postdate ago <br> <hr></td>";
            echo "<td align='justify'>$expires<hr></td>";
            echo "<td align='justify'>$gethits<hr></td>";
            echo "<td align='justify'>$getsyntax<hr></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} // end of class

class memberspost extends profile
{

    function _construct($username = null)
    {
        $this->u = $username;
    }

    function getallpost($theuserid)
    {
        $theuserid = $theuserid;
        $sql = "SELECT * FROM userp_$theuserid WHERE viewable = '1'";
        $result = mysql_query($sql);
        while ($row = mysql_fetch_array($result)) {
            $this->getpid = $row['postid'];
            $this->mytime = strtotime($row['post_date']);
            $this->postdate = $this->time_since($this->mytime);
            $this->getsyntax = $row['post_syntax'];
            $pid = $this->pid;
            $this->getallptitle = $row['post_title'];
            $this->plist = "<tr>";
            $this->plist .= "<td><a href='/phpbin/$this->getpid'>$this->getallptitle</a></td>";
            $this->plist .= "<td>$this->postdate ago</td>";
            $this->plist .= "<td>$this->getsyntax";
            $this->plist .= "</tr></td></table>";
            $this->userpost = $this->plist;
        }

    }

    // time since
    function time_since($original)
    {
        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
        );

        $today = time(); /* Current unix time  */
        $since = $today - $original;

        // $j saves performing the count function each time around the loop
        for ($i = 0, $j = count($chunks); $i < $j; $i++) {

            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0) {
                // DEBUG print "<!-- It's $name -->\n";
                break;
            }
        }


        $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";


        // if ($i + 1 < $j) {
        // now getting the second item
        //  $seconds2 = $chunks[$i + 1][0];
        //$name2 = $chunks[$i + 1][1];

        // add second item if it's greater than 0
        // if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
        //  $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        //  }
        //  }

        return $print;
    }
}