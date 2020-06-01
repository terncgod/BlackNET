<?php

/*
a class that handles Reset Password Requsts
 */
class ResetPassword
{
    private $db;
    private $user;

    public function __construct()
    {
        $this->db = new Database;
        $this->user = new User;
    }
    // generate a token
    public function generateToken()
    {
        return sha1(base64_encode(uniqid()));
    }

    // send an email to the user with the password link
    public function sendEmail($username)
    {
        $sendmail = new Mailer;
        try {
            if ($this->user->checkUser($username) !== "User Exist") {
                return false;
            } else {
                $token = $this->generateToken();
                $rows = $this->user->getUserData($username);
                $email = $rows->email;

                $this->db->query("INSERT INTO confirm_code (username,token) VALUES (:username,:token)");

                $this->db->bind(":username", $rows->username, PDO::PARAM_STR);
                $this->db->bind(":token", $token, PDO::PARAM_STR);

                if ($this->db->execute()) {
                    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $this->getDir();
                    $sendmail->sendmail($email, "Reset password instructions", "
			Hello $rows->username
			<br /><br />
			You recently made a request to reset your BlackNET account password. Please click the link below to continue.
			<br /><br />
			<a href='" . $actual_link . "reset.php?key=$token'>Update my password.</a>
			<br /><br />
			This link will expire in 24 hours
			<br /><br />
			If you did not make this request, please ignore this email.");
                }
                return true;
            }
        } catch (Exception $e) {
        }
    }

    public function updatePassword($key, $username, $password)
    {
        if (strlen($password) >= 8) {
            $this->db->query("UPDATE admin SET password = :password WHERE username = :username");

            $this->db->bind(":password", password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
            $this->db->bind(":username", $username, PDO::PARAM_STR);

            if ($this->db->execute()) {
                $this->deleteToken($key);
                return "Password Has Been Updated";
            }
        } else {
            return 'Please enter more then 8 characters';
        }
    }

    public function getUserAssignToToken($token)
    {
        $this->db->query("SELECT username FROM confirm_code WHERE token = :token limit 1");

        $this->db->bind(":token", $token, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        }
    }

    public function deleteToken($token)
    {
        try {
            $this->db->query("DELETE FROM confirm_code WHERE token = :token");

            $this->db->bind(":token", $token, PDO::PARAM_STR);

            if ($this->db->execute()) {
                return 'Client Removed';
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function isExist($token)
    {
        try {

            $this->db->query("SELECT * FROM confirm_code WHERE token = :token");

            $this->db->bind(":token", $token, PDO::PARAM_STR);

            if ($this->db->execute()) {
                if ($this->db->rowCount()) {
                    if ($this->isExpired($token) !== "Key expired") {
                        return "Key Exist";
                    } else {
                        return "Key expired";
                    }
                } else {
                    return "Key does not exist";
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function isExpired($key)
    {
        try {

            $this->db->query("SELECT * FROM confirm_code WHERE token = :token");

            $this->db->bind(":token", $key, PDO::PARAM_STR);

            if ($this->db->execute()) {
                $data = $this->db->single();

                $diff = time() - strtotime($data->created_at);

                if (round($diff / 60) >= 10) {
                    $this->deleteToken($key);
                    return "Key expired";
                } else {
                    return "Key is good";
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function getDir()
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $url);
        $dir = $_SERVER['SERVER_NAME'];
        for ($i = 0; $i < count($parts) - 1; $i++) {
            $dir .= $parts[$i] . "/";
        }
        return $dir;
    }
}
