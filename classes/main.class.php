<?php
declare(strict_types=1);

/**
 * Main Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.1.0-modern
 */

class Main
{
    private mysqli $db;
    private array $config;

    public function __construct(mysqli $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Clean and sanitize input
     *
     * @param string|null $var
     * @return string
     */
    public function clean(?string $var): string
    {
        return htmlspecialchars(trim(strip_tags($var ?? '')), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a random string
     *
     * @param int $length
     * @return string
     */
    public function generateRandomString(int $length = 10): string
    {
        $characters = '1234567890abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    public function getIp(): string
    {
        $ipSources = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($ipSources as $key) {
            if (!empty($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }

        return '0.0.0.0';
    }

    /**
     * Create a short link using Bit.ly
     *
     * @param string $url
     * @param string $format
     * @return string
     */
    public function shortLink(string $url, string $format = 'txt'): string
    {
        $login = $this->config['bitly_username'] ?? '';
        $apiKey = $this->config['bitly_api'] ?? '';

        if ($login === '' || $apiKey === '') {
            return '';
        }

        $connectURL = sprintf(
            'https://api-ssl.bitly.com/v3/shorten?login=%s&apiKey=%s&uri=%s&format=%s',
            urlencode($login),
            urlencode($apiKey),
            urlencode($url),
            urlencode($format)
        );

        return $this->curlGetResult($connectURL);
    }

    /**
     * Fetch URL content using cURL
     *
     * @param string $url
     * @return string
     */
    private function curlGetResult(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data ?: '';
    }

    /**
     * Format date/time
     *
     * @param string $dateTime
     * @param string $format
     * @return string
     */
    public function formatDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dt = new DateTime($dateTime);
            return $dt->format($format);
        } catch (Exception $e) {
            return $dateTime; // fallback in case of invalid date
        }
    }
}
