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
          echo "<font size=\"3\" color='green'><strong>Dataset succesfully archived.</strong></font><br>";
          $archiveform->reset();
        } else {
          echo "<font size=\"3\" color='red'><strong>An error occurred.</strong></font><br>";
        }
      }
    }
    $archiveform->display();
  }

}

?>