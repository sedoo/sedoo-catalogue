<?php
require_once "conf/conf.php";
if (!isset($_SESSION)) {
  session_start();
}

require_once 'conf/define-project.php';

$project_url = "/";
$titreMilieu = "";
ob_start();
include "loginPub.php";
include "extract/frmdownload-extract-pub.php";

$milieu = ob_get_clean();
include "template.php";
