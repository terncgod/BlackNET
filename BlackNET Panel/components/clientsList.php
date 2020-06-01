          <div class="card mb-3">
            <div class="card-header">
              <i class="fas  fa-user-circle"></i>
              Bot/Slaves List</div>
            <div class="card-body">
              <div class="pl-2 pb-2 pt-2 mb-3 border rounded">
                Toggle Column:
                <?php $i = 1;?>
                <?php foreach ($columns as $column): ?>
                  <a href="" class="toggle-vis" data-column="<?php echo $i; ?>" data-label-text="<?php echo $column ?>" ><?php echo $column ?></a> |
                  <?php $i++;?>
                  <?php endforeach;?>
              </div>
              <div class="table-responsive border pl-2 pb-2 pt-2 pr-2 pb-2 rounded">
                <table class="table nowrap table-bordered" width="100%" id="dataTable">
                  <thead>
                    <tr>
                      <th><input <?php if (empty($allClients)) {
    echo "disabled";
}?> type="checkbox" name="select-all" id="select-all"></th>
                    <?php foreach ($columns as $column): ?>
                      <th><?php echo $column; ?></th>
                    <?php endforeach;?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($allClients as $clientData): ?>
                      <tr class="<?php if ($clientData->is_usb === "yes"): ?>text-primary<?php endif;?>">
                        <td><input type="checkbox" id="client[]" name="client[]" value="<?php echo $clientData->vicid; ?>"></td>
                        <td><a href="viewuploads.php?vicid=<?php echo $clientData->vicid ?>"><?php echo $clientData->vicid; ?></a></td>
                        <td><?php echo $clientData->ipaddress; ?></td>
                        <td><?php echo $clientData->computername; ?></td>
                        <td><?php echo $clientData->is_admin; ?></td>
                        <td class="text-center">
                          <?php if ($countries[strtoupper($clientData->country)] === "Unknown"): ?>
                            <img src="flags/X.png">
                          <?php else: ?>
                            <img src="flags/<?php echo $clientData->country; ?>.png">
                          <?php endif;?>
                          <p hidden><?php echo $countries[strtoupper($clientData->country)]; ?></p>
                        </td>
                        <td><?php echo $clientData->os; ?></td>
                        <td><?php echo $clientData->insdate; ?></td>
                        <td><?php echo $clientData->antivirus; ?></td>
                        <td><span class="badge badge-primary"><?php echo $clientData->version; ?></span></td>
                        <td class="align-content-center text-center">
                          <img src="imgs/<?php echo strtolower($clientData->status) ?>.png">
                          <p hidden><?php $clientData->status;?></p>
                        </td>
                      </tr>
                    <?php endforeach;?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>