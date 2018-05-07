<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/define-project.php'; ;
$project_url = "/";
require_once 'conf/conf.php';
$titreMilieu = "";
ob_start();
include "news.php";

$milieu = ob_get_clean();
include "template.php";
?>
