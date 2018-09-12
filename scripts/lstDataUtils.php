<?php

require_once "utils/elastic/ElasticSearchUtils.php";
require_once 'bd/dataset.php';
require_once "bd/url.php";

/*
 * Retourne une liste des liens vers les données à afficher.
 */
function getAvailableDataLinks($dts, $project_name)
{
    $nodeConf = getDataNodeConf($dts, $project_name);
    $liste = array();
    if (isset($nodeConf['dataLink'])) {
        $liste[] = '<a href="' . $nodeConf['dataLink'] . '"><span class="icon-folder-open" data-color="Blue"></span> ' . $nodeConf['dataTitle'] . '</a>';
    }
    if (isset($nodeConf['extDataLink'])) {
        $liste[] = '<a href="' . $nodeConf['extDataLink'] . '" target="_blank"><span class="icon-folder-open" data-color="Purple"></span> ' . $nodeConf['extDataTitle'] . '</a>';
    }
    if (isset($nodeConf['bdLink'])) {
        $liste[] = '<a href="' . $nodeConf['bdLink'] . '"><span class="icon-folder-open" data-color="Green"></span> ' . $nodeConf['bdTitle'] . '</a>';
    }
    if (isset($nodeConf['qlLink'])) {
        $liste[] = '<a href="' . $nodeConf['qlLink'] . '" target="_blank"><span class="icon-folder-open" data-color="Orange"></span> ' . $nodeConf['qlTitle'] . '</a>';
    }

    return $liste;
}

/*
 * $queryArgs: arguments à ajouter à l'url du jeu
 */
function printDataset($dts, $queryArgs = array(), $withTitle = false)
{
    global $project_name;
    return ElasticSearchUtils::printDataset($dts->dats_id, $dts->dats_title, $dts->isInsertedDataset(), $project_name, $queryArgs, $withTitle);
}

/*
 * Affiche la liste des fiches d'un projet.
 * $proj: objet project ou nom d'un projet
 */
function lstProjectData($proj, $withTitle = true)
{

    if ($proj instanceof project) {
        $projName = $proj->project_name;
        $where = "WHERE project_id = $proj->project_id";
    } else {
        $projName = $proj;
        $where = "WHERE project_name = '$proj'";
    }

    if ($withTitle) {
        echo "<h1>$projName datasets</h1>";
        include 'legende.php';
    }
    $query = "SELECT dats_id,dats_title FROM dataset JOIN dats_proj USING (dats_id) JOIN project USING (project_id) $where AND (is_archived is null OR NOT is_archived) ORDER BY dats_title";
    lstQueryData($query);
}

function lstQueryData($query, $queryArgs = array())
{
    $dts = new dataset();
    $dts_list = $dts->getOnlyTitles($query);

    if (empty($dts_list)) {
        echo "<span class='danger'>No dataset found</span>";
    } else {
        echo "<ul>";
        foreach ($dts_list as $dt) {
            echo '<li>' . printDataset($dt, $queryArgs) . '</li>';
        }
        echo "</ul>";
    }
}

function getDataNodeConf($dts, $projectName, $queryArgs = array())
{
    return ElasticSearchUtils::getDataNodeConf($dts->dats_id, $dts->dats_title, $dts->isInsertedDataset(), $projectName, $queryArgs);
}

function addDataset(&$node, $dts, $projectName)
{
    $nodeConf = getDataNodeConf($dts, $projectName);
    $subnode = new HTML_TreeNode($nodeConf);
    $node->addItem($subnode);
}

function get_av_datasets(&$node, &$datasets)
{
    if (isset($node->items) && count($node->items) > 0) {
        for ($i = 0, $size = count($node->items); $i < $size; $i++) {
            get_av_datasets($node->items[$i], $datasets);
        }
    } else {
        if (!empty($node->bdLink)) {
            $datasets[] = $node->datsId;
        }
    }
}

/**
 * Légende pour les couleurs des dossiers
 *
 * @return array
 */
function getFolderLegend()
{

    $legende = array();
    if (constant('HasBlueTag') == 'true') {
        $legende['Blue'] = 'the dataset provided by the principal investigator.';
    }
    
    if (constant('HasGreenTag') == 'true') {
        $legende['Green'] = 'the homogenized dataset.';
    }
    
    if (constant('HasPurpleTag') == 'true') {
        $legende['Purple'] = 'data in another database.';
    }
    
    if (constant('HasOrangeTag') == 'true') {
        $legende['Orange'] = 'the campaign website quicklook charts.';
    }

    return $legende;
}
