<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/define-project.php'; ;
$project_url = "/Cerdanya";
require_once 'conf/conf.php';
$titreMilieu = "Model outputs request";
ob_start();
$_REQUEST['requested'] = true;
include "loginCat.php";
include "frmmod.php";

$milieu = ob_get_clean();
include "template.php";
?>
