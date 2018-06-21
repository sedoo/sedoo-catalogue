<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/conf.php';

require_once 'conf/define-project.php'; ;
$project_url = "/Cerdanya";
$titreMilieu = "In Situ Data Request";
ob_start();
$_REQUEST['requested'] = true;

include "loginCat.php";
include "frminstr.php";

$milieu = ob_get_clean();
include "template.php";
?>
