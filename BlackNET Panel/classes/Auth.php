<?php
/*
this class is to handle user authentication

how to use
$auth = new Auth
$auth->newLogin($_POST['username'],$_POST['password']);
 */
class Auth
{

    private $db;
    private $user;

    public function __construct()
    {
        $this->db = new Database;
        $this->user = new User;
    }
    // function to update 2fa secret if needed
    public function updateSecret($username, $secret)
    {
        $this->db->query("UPDATE admin SET secret = :secret WHERE username = :username");

        $this->db->bind(":secret", $secret, PDO::PARAM_STR);
        $this->db->bind(":username", $username, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return "Secret Updated";
        }
    }

    // check login information with brute force protection
    public function newLogin($username, $password)
    {
        $total_failed_login = 5;
        $lockout_time = 10;
        $account_locked = false;
        $status_code = null;

        $this->db->query('SELECT failed_login, last_login FROM admin WHERE username = (:user) LIMIT 1;');
        $this->db->bind(':user', $username, PDO::PARAM_STR);

        $this->db->execute();

        $row = $this->db->single();

        if (($this->db->rowCount() === 1) && ($row->failed_login >= $total_failed_login)) {
            $last_login = strtotime($row->last_login);
            $timeout = $last_login + ($lockout_time * 60);
            $timenow = time();

            if ($timenow < $timeout) {
                $account_locked = true;
                return 403;
            }

        }

        $this->db->query('SELECT * FROM admin WHERE username = (:user) LIMIT 1;');

        $this->db->bind(':user', $username);

        $this->db->execute();

        $row = $this->db->single();

        if (($this->db->rowCount() === 1) && (password_verify($password, $row->password)) && ($account_locked === false)) {
            $failed_login = $row->failed_login;
            $last_login = $row->last_login;

            $this->db->query('UPDATE admin SET failed_login = "0" WHERE username = (:user) LIMIT 1;');

            $this->db->bind(':user', $username, PDO::PARAM_STR);

            $this->db->execute();

            return 200;
        } else {
            sleep(rand(2, 4));

            $this->db->query('UPDATE admin SET failed_login = (failed_login + 1) WHERE username = (:user) LIMIT 1;');

            $this->db->bind(':user', $username, PDO::PARAM_STR);

            $this->db->execute();

            return 401;
        }

        $this->db->query('UPDATE admin SET last_login = now() WHERE username = (:user) LIMIT 1;');

        $this->db->bind(':user', $username, PDO::PARAM_STR);

        $this->db->execute();
    }

    // Google Recaptcha API to validate recaptcha v2 response
    public function recaptchaResponse($privatekey, $recaptcha_response_field)
    {
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $privatekey . '&response=' . $recaptcha_response_field);
        $responseData = json_decode($verifyResponse);
        return $responseData;
    }

    // check if 2fa is enbaled
    public function isTwoFAEnabled($username)
    {
        $data = $this->user->getUserData($username);
        return $data->s2fa;
    }

    public function getUserData($username)
    {
        $data = $this->user->getUserData($username);
        return $data;
    }

    // Return the user 2fa secret
    public function getSecret($username)
    {
        $data = $this->user->getUserData($username);
        return $data->secret;
    }
}
