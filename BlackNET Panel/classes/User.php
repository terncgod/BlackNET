<?php

class User
{
    // a class that handle user function and settings
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // get user data
    public function getUserData($username)
    {
        $this->db->query("SELECT * FROM admin WHERE username = :username or email = :email");

        $this->db->bind(":username", $username, PDO::PARAM_STR);
        $this->db->bind(":email", $username, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        }
    }

    // count all users
    public function numUsers()
    {
        $this->db->query("SELECT * FROM admin");

        if ($this->db->execute()) {
            return $this->db->rowCount();
        }
    }

    //check if user exist
    public function checkUser($username)
    {
        $this->db->query("SELECT * FROM admin WHERE username = :username or email = :email");

        $this->db->bind(":username", $username, PDO::PARAM_STR);
        $this->db->bind(":email", $username, PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->rowCount()) {
                return 'User Exist';
            } else {
                return 'Permission Denied';
            }

        }

    }

    // update a user data
    public function updateUser($id, $oldusername, $username, $email, $password, $auth, $question, $answer, $sqenable)
    {

        $user = $this->getUserData($oldusername);

        if ($password === "No change") {
            $password = $user->password;
        } else {
            $password = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->query("UPDATE admin SET
        username = :username,
        email = :email,
        password = :password,
        sqenable = :sqenable,
        question = :question,
        answer = :answer
        WHERE id = :id");

        $this->db->bind(":username", $username, PDO::PARAM_STR);
        $this->db->bind(":email", $email, PDO::PARAM_STR);
        $this->db->bind(":password", $password, PDO::PARAM_STR);
        $this->db->bind(":sqenable", $sqenable, PDO::PARAM_STR);
        $this->db->bind(":question", $question, PDO::PARAM_STR);
        $this->db->bind(":answer", $answer, PDO::PARAM_STR);
        $this->db->bind(":id", $id, PDO::PARAM_INT);

        if ($this->db->execute()) {
            return 'Username Updated';
        }
    }

    // Function that enabled 2FA
    public function enables2fa($username, $secret, $auth)
    {

        if ($auth === "off") {
            $secret = "null";
        }

        $this->db->query("UPDATE admin SET s2fa = :auth, secret = :secret WHERE username = :username");

        $this->db->bind(":auth", $auth, PDO::PARAM_STR);
        $this->db->bind(":secret", $secret, PDO::PARAM_STR);
        $this->db->bind(":username", $username, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return true;
        }
    }

    // get question using $username
    public function getQuestionByUser($username)
    {
        $this->db->query("SELECT question,answer,sqenable FROM admin WHERE username = :user");

        $this->db->bind(":user", $username, PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        }
    }

    // check if securiry question enabled
    public function isQuestionEnabled($username)
    {
        try {
            $this->db->query("SELECT sqenable FROM admin WHERE username = :user");

            $this->db->bind(":user", $username, PDO::PARAM_STR);

            if ($this->db->execute()) {
                $data = $this->db->single();
                if ($data->sqenable === "off") {
                    return false;
                } else {
                    return true;
                }
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
