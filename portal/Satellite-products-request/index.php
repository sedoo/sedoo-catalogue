<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/conf.php';
require_once 'conf/define-project.php'; ;
$project_url = "/Cerdanya";
$titreMilieu = "Satellite products request";
ob_start();
$_REQUEST['requested'] = true;

include "loginCat.php";
include "frmsatsimple.php";

$milieu = ob_get_clean();
include "template.php";
?>
