<?php

require_once "forms/suscribe_form.php";

$sform = new suscribe_form();
$sform->createForm();

if ($sform->isPortalUser()) {
    $datsId = $_REQUEST['datsId'];
    $rubId = $_REQUEST['rubriqueId'];
    if (isset($datsId) && !empty($datsId)) {
        if ($sform->addAbo($datsId)) {
            echo '<p><span class="success">You will be informed by email when new data are available for this dataset.</span></p>';
        } else {
            echo '<p><span class="danger">We were unable to proceed this request.</span></p>';
        }
    }
} else {
    $sform->displayLGForm("", false);
}
