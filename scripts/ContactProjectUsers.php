<?php
require_once "forms/Contact_Users_form.php";

$ContactUsersform = new Contact_Users_form();
$ContactUsersform->createForm();

if (isset($_POST['bouton_send'])) {
    if ($ContactUsersform->validate()) {
        $ContactUsersform->sendMessageToAllUsers($project_name);
        echo "<h1><span class='success'>Your message has been sent successfully</span></h1><br>";
    } else {
        echo "<h1>Contact all $project_name users</h1><br>";
        $ContactUsersform->display();
    }
} else {
    echo "<h1>Contact all $project_name users</h1><br>";
    $ContactUsersform->display();
}
