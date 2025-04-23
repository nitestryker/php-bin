<?php
declare(strict_types=1);

/**
 * Registration Class
 *
 * @package PHP-Bin
 * @author Jeremy Stevens
 * @copyright 2014-2023 Jeremy Stevens
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @version 2.1.0
 */

class Registration 
{
    private mysqli $db;
    private array $config;

    public function __construct(mysqli $db, array $config) 
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function showForm(): void 
    {
        require 'templates/registration.tpl.php';
    }

    public function registerUser(): bool 
    {
        try {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if (!$email || !$this->isValidEmail($email)) {
                throw new InvalidArgumentException('Please enter a valid email address');
            }

            $stmt = $this->db->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new RuntimeException('Email address already exists');
            }

            $uid = $this->generateRandomString(9);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "INSERT INTO users (uid, username, password, email, join_date) 
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param("ssss", $uid, $username, $hashedPassword, $email);

            if (!$stmt->execute()) {
                throw new RuntimeException('Failed to create account');
            }

            $_SESSION['userid'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            return true;

        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function redirect(): void 
    {
        header('Location: index.php', true, 303);
        exit();
    }

    private function generateRandomString(int $length = 9): string 
    {
        return bin2hex(random_bytes(($length + 1) / 2));
    }

    private function isValidEmail(string $email): bool 
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && 
               checkdnsrr(substr(strrchr($email, "@"), 1), "MX");
    }
}