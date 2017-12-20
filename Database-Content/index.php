<?php
	if (! isset ( $_SESSION ))
		session_start ();
	$project_name = explode ( '.', $_SERVER['SERVER_NAME'] )[0]; //"Cerdanya";;

	ob_start ();
	include ("database-content-user.php");
?>
