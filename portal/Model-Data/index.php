<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/define-project.php'; 
$project_url = "/";
$titreMilieu = "Model outputs registration";
ob_start();
include "loginCat.php";
include "frmmod.php";

$milieu = ob_get_clean();
include "template.php";
?>
