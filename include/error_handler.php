<?php
/**
 *  error.php
 *
 * @package phpbin
 * @author nitestryker
 * @copyright 2015 Nitestryker
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * my_error_handler($errno, $errstr, $errfile, $errline)
 * @version 1.1
 * 
 * custom error handler
 *
 * Parameters:
 *  $errno:   Error level
 *  $errstr:  Error message
 *  $errfile: File in which the error was raised
 *  $errline: Line at which the error occurred
*/

// Destinations
define("LOG_FILE", "LOG.txt");
 
// Destination types
define("DEST_LOGFILE", "3");
 
 function error_handler($errno, $errstr, $errfile, $errline)
 {
 switch ($errno) {
    case E_USER_ERROR:
      // Send an e-mail to the administrator
      error_log("Error: $errstr \n Fatal error on line $errline in file $errfile \n", DEST_EMAIL, ADMIN_EMAIL);
 
      // Write the error to our log file
      error_log("Error: $errstr \n Fatal error on line $errline in file $errfile \n", DEST_LOGFILE, LOG_FILE);
      break;
 
    case E_USER_WARNING:
      // Write the error to our log file
      error_log("Warning: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, LOG_FILE);
      break;
 
    case E_USER_NOTICE:
      // Write the error to our log file
      error_log("Notice: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, LOG_FILE);
      break;
 
    default:
      // Write the error to our log file
      error_log("Unknown error [#$errno]: $errstr \n in $errfile on line $errline \n", DEST_LOGFILE, LOG_FILE);
      break;
  }
 
  // Don't execute PHP's internal error handler
  return TRUE;	
 }
?>