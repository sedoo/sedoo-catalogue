<?php
if (! isset ( $_SESSION ))
	session_start ();
require_once ('conf/conf.php');
$project_name = explode ( '.', $_SERVER['SERVER_NAME'] )[0]; //"Cerdanya";;
$project_url = "/";
$titreMilieu = "";
ob_start ();
include ("lstDataByCountry.php");
?>
<?php

$milieu = ob_get_clean ();
include ("template.php");
?>
