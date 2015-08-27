<?php
/**
 * archive.php
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2015 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 1.0.8
*/

// Get everything started up...

$time_start = microtime();

ob_start();

// check if session is already started PHP >= 5.4.0
if(session_id() == '') {
    session_start();
}

include_once 'include/error_handler.php';
include 'templates/archive.tpl.php';

?>