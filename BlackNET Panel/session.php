<?php
session_start();
include_once __DIR__ . '/classes/Database.php';
include_once APP_PATH . '/classes/User.php';
include_once APP_PATH . '/classes/Auth.php';
include_once APP_PATH . '/classes/Utils.php';

$utils = new Utils;

$user = new User;

$auth = new Auth;

if (isset($_SESSION)) {

    $username = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : null;

    if ($username !== null) {
        if (isset($_SESSION['login_user']) && $username !== null) {
            $data = $auth->getUserData($username);
        }

        if (!isset($_SESSION['current_ip'])) {
            $_SESSION['current_ip'] = $utils->sanitize($_SERVER['REMOTE_ADDR']);
        }

        if (!(isset($_SESSION['csrf']))) {
            $_SESSION['csrf'] = hash('sha256', uniqid() . $_SESSION["current_ip"] . session_id());
        }

        if (!isset($_SESSION['login_user']) || !isset($_SESSION["current_ip"])) {
            $utils->redirect("login.php");
        }

        if ($auth->isTwoFAEnabled($username) === "on") {
            if (!isset($_SESSION['OTP']) || $_SESSION['OTP'] !== "OK") {
                $utils->redirect("logout.php");
            }
        }

    } else {
        $utils->redirect("login.php");

    }

}
