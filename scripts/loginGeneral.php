<?php

require_once "forms/login_form.php";
require_once 'ldap/portalUser.php';
require_once 'forms/logout_form.php';
require_once 'ldap/user.php';

$form = new login_form();
$form->createLoginForm('mail');

if (isset($_SESSION['loggedUser'])) {
    $form->user = unserialize($_SESSION['loggedUser']);
} elseif (!isset($_POST['bouton_login'])) {
    $form->displayLoginButton();
}

if (isset($_POST['loginbutton'])) {
    $titreMilieu = "";
    ob_start();
    $form->displayLGForm('', true, '', '');
    $milieu = ob_get_clean();
}

//Action logout
elseif (isset($_POST['logout'])) {
    session_destroy();
    $form->user = null;
}
//Action login
elseif (isset($_POST['bouton_login'])) {
    if ($form->validate()) {
        if ($form->loginCat() === false) {
            $titreMilieu = "";
            ob_start();
            $form->displayLGForm('', true, '', '');
            $milieu = ob_get_clean();
        } elseif (isset($form->user)) {
            $formLogout = new logout_form();
            $formLogout->createForm();
            $formLogout->displayForm($form->user, $project_name);
        }
    }
    $form->saveErrors();
} elseif (isset($_POST['bouton_forgot'])) {
    $titreMilieu = "";
    ob_start();
    $form->displayLGForm('', true, '', true);
    $milieu = ob_get_clean();
}
