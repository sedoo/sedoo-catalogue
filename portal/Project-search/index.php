<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'conf/define-project.php';
$project_url = "/";
$titreMilieu = "";
ob_start();
include "lstDataByProj.php";

$milieu = ob_get_clean();
include "template.php";
