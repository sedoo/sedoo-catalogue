<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'conf/define-project.php';
$project_url = "/";
$titreMilieu = "Value Added Dataset";
ob_start();
include "loginCat.php";
include "frmvadataset.php";

$milieu = ob_get_clean();
include "template.php";
