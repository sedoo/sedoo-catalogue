<?php

require_once "forms/journal_form.php";

if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
    $typeJournal = $_REQUEST['type'];
} else {
    $typeJournal = 0;
}
$jform = new journal_form();
$jform->createForm(false, $typeJournal);
$jform->projectName = $project_name;
if ($jform->isRoot()) {
    if (isset($_REQUEST['add']) && !empty($_REQUEST['add'])) {
        if (isset($_POST['bouton_add'])) {
            if ($jform->validate()) {
                if ($jform->addEntry()) {
                    echo "<span class='success'><strong>Entry succesfully inserted.</strong></span><br>";
                    $jform->resetAddForm();
                } else {
                    echo "<span class='danger'><strong>An error occurred.</strong></span><br>";
                }
            }
        }
        $jform->displayAddForm($typeJournal);
    } else {
        $jform->displayList($typeJournal);
    }
} elseif ($jform->isLogged()) {
    echo "<h1>Admin Corner</h1>";
    echo "<span class='danger'><strong>You cannot view this part of the site.</strong></span><br>";
} else {
    $jform->displayLGForm("", true);
}
