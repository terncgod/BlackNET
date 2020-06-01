<?php
include_once 'classes/Utils.php';

$utils = new Utils;

session_start();

if (session_destroy()) {
    $utils->redirect("login.php");
}
