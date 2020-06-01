<?php
include_once 'classes/Utils.php';
include_once 'classes/blackupload/Upload.php';

$utils = new Utils;

if (isset($_GET['id'])) {
    $folder = "upload/" . $utils->sanitize($utils->base64_decode_url($_GET['id']));
}

$upload = new BlackUpload\Upload($_FILES['file'], realpath($folder), "classes/blackupload/");

$upload->setINI(
    [
        "file_uploads" => 1,
        "memory_limit" => "2048MB",
        "upload_max_filesize" => "2048MB",
        "post_max_size" => "2048MB",
    ]
);

$upload->enableProtection();

try {
    if (
        $upload->checkForbidden() &&
        $upload->checkExtension() &&
        $upload->checkMime()
    ) {
        $upload->upload();
    }
} catch (Throwable $th) {
}
