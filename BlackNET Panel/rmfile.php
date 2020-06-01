<?php
include_once 'session.php';
try {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $files = $_POST['file'];
        if ($_SESSION['csrf'] === $utils->sanitize($_POST['csrf'])) {
            foreach ($files as $file) {
                if (strpos($file, "..")) {
                    $file = str_replace(["..", "/"], [null, null], $file);
                }
                $filename = $utils->sanitize(stripcslashes($file));
                @unlink(realpath("upload/" . trim($_POST['vicid']) . "/" . $filename));
            }
            $utils->redirect("viewuploads.php?vicid=" . $utils->sanitize($_POST['vicid']) . "&msg=yes");
        } else {
            $utils->redirect("viewuploads.php?vicid=" . $utils->sanitize($_POST['vicid']) . "&msg=csrf");
        }
    } else {
        if (strpos($file, "..")) {
            $file = str_replace(["..", "/"], [null, null], $file);
        }
        $filename = $utils->sanitize(stripcslashes($file));
        if ($_SESSION['csrf'] === $utils->sanitize($_GET['csrf'])) {
            @unlink(realpath("upload/" . trim($_GET['vicid']) . "/" . $filename));
            $utils->redirect("viewuploads.php?vicid=" . $utils->sanitize($_GET['vicid']) . "&msg=yes");
        } else {
            $utils->redirect("viewuploads.php?vicid=" . $utils->sanitize($_GET['vicid']) . "&msg=csrf");
        }
    }
} catch (Exception $e) {
}
