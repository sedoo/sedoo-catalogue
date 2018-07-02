<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'conf/define-project.php';
$project_url = "/";
$titreMilieu = "User Account Creation";
ob_start();
include "frmregisterMultiProjects.php";

$milieu = ob_get_clean();
include "template.php";
