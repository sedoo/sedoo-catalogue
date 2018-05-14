<?php

require_once "forms/duplicate_dataset_form.php";

$dupli_dataset_form = new duplicate_dataset_form();
$dupli_dataset_form->createForm($project_name);

if (isset($_SESSION['loggedUser']) && !empty($_SESSION['loggedUser'])) {
  $dupli_dataset_form->user = unserialize($_SESSION['loggedUser']);
}

if ($dupli_dataset_form->isLogged()) {
  if (isset($_POST['bouton_duplicate'])) {
    if ($dupli_dataset_form->validate()) {
      if ($dupli_dataset_form->duplicate_dataset()) {
        echo "<span class='success'><strong>Dataset is succesfully duplicated, you can view it using the following link : <a href='/?editDatsId=" . $dupli_dataset_form->get_id() . "&datsId=" . $dupli_dataset_form->get_id() . "'>here</a></strong></span><br>";
        $dupli_dataset_form->reset_form();
      } else {
        echo "<span class='danger'><strong>An error occurred.</strong></span><br>";
      }
    }
  }
  $dupli_dataset_form->displayForm();
} else {
  echo "<span class='danger'><strong>You can't view this part of the portal because you are not logged !</strong></span><br>";
}
?>
