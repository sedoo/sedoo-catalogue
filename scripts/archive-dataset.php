<?php

require_once "forms/archive_form.php";

$archiveform = new archive_form();
$archiveform->createForm($project_name);

if ($archiveform->isRoot()) {
  if (isset($_REQUEST['datsId']) && !empty($_REQUEST['datsId'])) {
    $datsId = $_REQUEST['datsId'];
  }

  if (isset($datsId) && !empty($datsId)) {
    $archiveform->displayArchivedDataset($datsId);
  } else {

    if (isset($_POST['bouton_add'])) {
      if ($archiveform->validate()) {
        if ($archiveform->archive()) {
          echo "<span class='success'><strong>Dataset succesfully archived.</strong></span><br>";
          $archiveform->reset();
        } else {
          echo "<span class='danger'><strong>An error occurred.</strong></span><br>";
        }
      }
    }
    $archiveform->display();
  }

}

?>