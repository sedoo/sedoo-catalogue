<?php

require_once "forms/url_form.php";

if (array_key_exists('type', $_REQUEST)) {
    $typeUrl = $_REQUEST['type'];
} else {
    $typeUrl = 0;
}

$urlform = new url_form();
$urlform->createForm($typeUrl, $project_name);

if ($urlform->isRoot()) {
    foreach (array_keys($_POST) as $key) {
        if (strpos($key, 'bouton_update_') === 0) {
            $id = substr($key, 14);
            $urlform->updateUrl($id);
        }
        if (strpos($key, 'bouton_delete_all_') === 0) {
            $id = substr($key, 18);
            $urlform->deleteUrls($id);
        } elseif (strpos($key, 'bouton_delete_') === 0) {
          //Suppression d'une seule url
            $id = substr($key, 14);
            $urlform->deleteUrl($id);
        }
        if (strpos($key, 'bouton_update_roles_') === 0) {
            $id = substr($key, 20);
            $urlform->updateRoles($id);
        }
    }

    if (isset($_POST['bouton_ok'])) {
    } elseif (isset($_POST['bouton_add'])) {
        if ($urlform->validate()) {
            if ($urlform->addUrl()) {
                echo "<span class='success'><strong>URL succesfully inserted.</strong></span><br>";
            } else {
                echo "<span class='danger'><strong>An error occurred.</strong></span><br>";
            }
        }
    }
    $urlform->displayAddUrlForm();
}
