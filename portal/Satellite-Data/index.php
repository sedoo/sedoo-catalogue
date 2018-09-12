<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'conf/define-project.php';
$project_url = "/";
$titreMilieu = "Satellite products registration";
ob_start();

include "loginCat.php";
include "frmsat.php";

$milieu = ob_get_clean();
include "template.php";
