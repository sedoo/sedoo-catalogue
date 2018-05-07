<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/define-project.php'; 
$project_url = "/";
$titreMilieu = "<em>In situ</em> instrument registration";
ob_start();

include "loginCat.php";
include "frminstr.php";

$milieu = ob_get_clean();
include "template.php";
?>
