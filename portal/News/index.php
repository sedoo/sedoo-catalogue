<?php
if (! isset ( $_SESSION ))
	session_start ();
$project_name = explode ( '.', $_SERVER['SERVER_NAME'] )[0]; //"Cerdanya";;
$project_url = "/";
require_once ('conf/conf.php');
$titreMilieu = "";
ob_start ();
include ("news.php");
?>
<?php

$milieu = ob_get_clean ();
include ("template.php");
?>
