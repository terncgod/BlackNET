<?php
include_once 'classes/POST.php';
include_once 'classes/Utils.php';

$post = new POST;

$utils = new Utils;

$client = $utils->sanitize($_POST['clientid']);

$result = $utils->sanitize($_POST['result']);

$post->prepare(realpath("upload/$client"), "shell_result.txt", $result, "a+");

$post->write();
