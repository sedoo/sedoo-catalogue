<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once 'conf/define-project.php';
$project_url = "/";
$titreMilieu = "";
ob_start();
include "loginAdm.php";
include "frmadminPortal.php";
$milieu = ob_get_clean();
include "template-admin.php";
?>
