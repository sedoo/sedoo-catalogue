<?php
if (!isset($_SESSION)) {
  session_start();
}

$project_name = explode('.', $_SERVER['SERVER_NAME'])[0]; //"Cerdanya";
$project_url = "/";
$titreMilieu = "<em>In situ</em> instrument registration";
ob_start();

include "loginCat.php";
include "frminstr.php";

$milieu = ob_get_clean();
include "template.php";
?>
