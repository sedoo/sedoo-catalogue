<?php
include "conf/conf.php";

if (!isset($_SESSION)) {
  session_start();
}

// on récupère le nom du projet à partir de l'url
require_once 'conf/define-project.php'; 
$project_url = "/";
$titreMilieu = '';
ob_start();
include "lstDataByPlat.php";

$milieu = ob_get_clean();
include "template.php";
?>
