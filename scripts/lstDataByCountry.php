<?php
require_once('bd/bdConnect.php');
require_once('bd/gcmd_location_keyword.php');
require_once('filtreProjets.php');
require_once('lstDataUtils.php');
require_once('TreeMenu.php');

//echo "<h1>Datasets ordered by country</h1>";
include 'legende.php';
	
$tree = new HTML_TreeMenu();


$c = new gcmd_location_keyword();
$query = "select * from gcmd_location_keyword where gcmd_level = 5 or gcmd_level = 4 order by gcmd_loc_name;";
$country_list = $c->getByQuery($query);
//echo $query.'<br>';
	
foreach($country_list as $country){
	addCountry($tree,$country,$project_name);
}
addOthers($tree,$project_name);
$treeMenu = new HTML_TreeMenu_DHTML($tree,array('images' => '/scripts/images','defaultClass' => 'treeMenuDefault'));
$treeMenu->printMenu();

function addCountry(&$parent,$country,$project_name){
	$node = new HTML_TreeNode(array('text' => $country->gcmd_loc_name));
        $dts = new dataset;
	$projects = get_filtre_projets($project_name);
        $query = "select dats_id, dats_title from dataset where dats_id in (select distinct dats_id from dats_proj where project_id in ($projects)) and dats_id in (select distinct dats_id from dats_place where place_id in (select distinct place_id from place where gcmd_loc_id = $country->gcmd_loc_id)) AND (is_archived is null OR NOT is_archived) order by dats_title;";
        //echo $query.'<br>';
        $dts_list = $dts->getOnlyTitles($query);
        foreach ($dts_list as $dt) {
	        addDataset($node,$dt,$project_name);
        }
	if (!empty($dts_list))
	        $parent->addItem($node);
}


function addOthers(&$parent,$projectName){
	$node = new HTML_TreeNode(array('text' => 'Other'));
	$projects = get_filtre_projets($projectName);
	$query = "select dats_id, dats_title from dataset where dats_id in (select distinct dats_id from dats_proj where project_id in ($projects)) and dats_id in (select distinct dats_id from dats_place where place_id in (select distinct place_id from place where gcmd_loc_id is null)) AND (is_archived is null OR NOT is_archived) order by dats_title;";
	$dts = new dataset;
	$dts_list = $dts->getOnlyTitles($query);
	foreach ($dts_list as $dt){
		addDataset($node,$dt,$projectName);
	}
	if (count($dts_list) > 0)
		$parent->addItem($node);
}



?>

