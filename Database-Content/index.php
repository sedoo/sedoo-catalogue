<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once 'conf/define-project.php';

ob_start();
include "database-content-user.php";
