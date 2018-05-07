<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once 'conf/define-project.php'; 
$project_url = "/";
$titreMilieu = "";
ob_start();
include "utils/elastic/advancedSearch.php";
?>
<?php
$milieu = ob_get_clean();
include "template_map.php";
?>
