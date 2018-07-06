<?php
require_once 'forms/logout_form.php';
require_once 'forms/login_form.php';
require_once 'ldap/portalUser.php';
require_once 'ldap/user.php';

if (isset($_SESSION['loggedUser'])) {
    $user = unserialize($_SESSION['loggedUser']);
}

$form_logout = new logout_form();
$form_logout->createForm();
if (isset($_POST['logout'])) {
    session_destroy();
    $form_login = new login_form();
    $form_login->createLoginForm();
    $form_login->displayLoginButton();
} elseif (isset($user) && !empty($user)) {
    $form_logout->displayForm($user);
} else {
    include 'loginGeneral.php';
}
