<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/conf.php';

$project_name = explode('.', $_SERVER['SERVER_NAME'])[0]; //"Cerdanya";;
$project_url = "/Cerdanya";
$titreMilieu = "";
ob_start();
$_REQUEST['requested'] = true;

include "loginCat.php";
include "frminstr.php";

$milieu = ob_get_clean();
include "template.php";
?>
