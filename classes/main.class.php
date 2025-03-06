
<?php
/**
 * Main Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @version 2.0.0
 */

class Main
{
    private $db;
    private $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Clean and sanitize input
     * 
     * @param string $var Input to clean
     * @return string Cleaned input
     */
    public function clean($var = null)
    {
        if ($var === null) {
            return '';
        }
        
        $var = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        $var = trim($var);
        $var = strip_tags($var);
        return $var;
    }

    /**
     * Generate a random string
     *
     * @param int $length Length of the random string
     * @return string Random string
     */
    public function generateRandomString($length = 10)
    {
        $characters = "1234567890abcdefghijklmnopqrstuvwxyz";
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }

    /**
     * Get client IP address
     *
     * @return string IP address
     */
    public function getIp()
    {
        // Check for forwarded IP
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && 
            filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Check for client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && 
            filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        // Default to remote address
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Create a short link using Bit.ly
     *
     * @param string $url The URL to shorten
     * @param string $format Output format
     * @return string Shortened URL
     */
    public function shortLink($url, $format = 'txt')
    {
        $login = $this->config['bitly_username'];
        $apiKey = $this->config['bitly_api'];
        
        if (empty($login) || empty($apiKey)) {
            return '';
        }
        
        $connectURL = 'https://api-ssl.bitly.com/v3/shorten?login=' . $login . 
                      '&apiKey=' . $apiKey . '&uri=' . urlencode($url) . 
                      '&format=' . $format;
                      
        return $this->curlGetResult($connectURL);
    }
    
    /**
     * Fetch URL content using cURL
     *
     * @param string $url URL to fetch
     * @return string Response
     */
    private function curlGetResult($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    /**
     * Format date/time
     *
     * @param string $dateTime Date/time string
     * @param string $format Output format
     * @return string Formatted date/time
     */
    public function formatDateTime($dateTime, $format = 'Y-m-d H:i:s')
    {
        $dt = new DateTime($dateTime);
        return $dt->format($format);
    }
}
?>
