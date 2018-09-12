<?php
require_once "bd/url.php";
require_once 'gmap/map_form.php';
require_once "bd/journal.php";
require_once "sortie/print_utils.php";
require_once "bd/dataset_factory.php";
require_once "scripts/lstDataUtils.php";
require_once("conf/doi.conf.php");

function editContact(&$pis)
{
    foreach ($pis as $pi) {
        if ($pi->personne->pers_email_1) {
            $mail = explode('@', strtolower($pi->personne->pers_email_1));
            $mail2 = explode('.', $mail[1]);
            $i = strrpos($mail[1], '.');
            $tld = substr($mail[1], $i + 1);
            $d = substr($mail[1], 0, $i);
            $label = ucwords(strtolower($pi->personne->pers_name)) . ' - ' . $pi->personne->organism->getName() . ' (' . $pi->contact_type->contact_type_name . ')';

            $tldIds = array(
            'com' => 0,
            'org' => 1,
            'net' => 2,
            'ws' => 3,
            'info' => 4,
            'int' => 5,
            'edu' => 6,
            'gov' => 7,
            'uk' => 10,
            'fr' => 14,
            'es' => 15,
            'de' => 16,
            'at' => 17,
            'it' => 18,
            'cat' => 19,
            'ch' => 20,
            'hr' => 21,
            'ro' => 22,
            'il' => 23,
            'nl' => 24,
            'gr' => 25);

            if (array_key_exists($tld, $tldIds)) {
                  $tldId = $tldIds[$tld];
                  echo '<script>mail2("' . $mail[0] . '","' . $d . '",' . $tldId . ',"","' . $label . '")</script><BR/>';
            } else {
                echo "<a href='mailto:" . $pi->personne->pers_email_1 . "'>";
                echo $label;
                echo "</a><BR/>";
            }
        }
    }
}

function editDOI($doi)
{
    if (isset($doi) && !empty($doi)) {
        echo "<tr><td><strong>Dataset DOI</strong></td><td colspan='3'>$doi";
        $f = fopen(DATACITE_CITATION . $doi, 'r');
        if ($f) {
            echo '<p><strong>How to cite: </strong>' . fgets($f) . '</p>';
        }
        echo '<a target="_blank" class="btn_tag" href="' . DATACITE_BIBTEX . $doi . '" title="Export to BibTeX">Export to BibTeX</a>';
        echo "</td></tr>";
    }
}

function editDataAvailability(&$dataset, $projectName, $queryArgs = array())
{
    $liens = getAvailableDataLinks($dataset, $projectName, $queryArgs);
    if (isset($liens) && !empty($liens)) {
        echo '<tr><td rowspan="' . count($liens) . '"><strong>Data access</strong></td>';
        foreach ($liens as $lien) {
            echo "<td colspan='3'>$lien</td></tr>";
        }
      //Historique du jeu
        $journal = new journal();
        $journal = $journal->getByDataset($dataset->dats_id, TYPE_NEW . ',' . TYPE_UPDATE);
        if (isset($journal) && !empty($journal)) {
            echo '<tr><td><strong>History</strong></td><td colspan="3" style="padding-right:0px;">';
            if (count($journal) > 3) {
                echo '<div style="overflow:auto;height:150px;">';
            }
            foreach ($journal as $jEntry) {
                echo '<p>';
                if ($jEntry->type_id == TYPE_NEW) {
                    echo '<span class="pink_tag">ISSUE ';
                } elseif ($jEntry->type_id == TYPE_UPDATE) {
                    echo '<span class="lightpink_tag">UPDATE ';
                }
                echo $jEntry->date->format('Y-m-d') . '</span>';
                if (isset($jEntry->comment) && !empty($jEntry->comment)) {
                    echo $jEntry->comment;
                }
                echo '</p>';
            }
            if (count($journal) > 3) {
                echo '</div>';
            }
            echo '</td></tr>';
        }
    } else {
        echo '<tr><td><strong>Data availability</strong></td>';
        echo '<td colspan="3">No data are currently available for this dataset.&nbsp;';
        $suscribeUrl = '/Your-Account?p&pageId=6&datsId=' . $dataset->dats_id;
        if (isset($_SESSION['loggedUserAbos'])) {
            $aboIds = unserialize($_SESSION['loggedUserAbos']);
            if (!isset($aboIds)) {
                $aboIds = array();
            }

            if (array_search($dataset->dats_id, $aboIds) === false) {
                echo "<a href='$suscribeUrl'>Click here to receive an email when this dataset becomes available</a>";
            } else {
                echo "&nbsp;You will be informed by email when this dataset becomes available.";
            }
        } else {
            echo "<a href='$suscribeUrl'>Click here to receive an email when this dataset becomes available</a>";
        }
        echo '</td></tr>';
    }
}

/*
 * Remplace les retours à la ligne par la balise br, sauf si le texte commence par une balise.
 */
function tmp_format_abstract($string)
{
    $string = trim($string);
    if (strpos($string, '<') === 0) {
        return $string;
    } else {
        return nl2br($string);
    }
}

function editDataDescr(&$dataset)
{
    echo '<tr><th colspan="4" align="center"><strong>Data description</strong></th></tr>';
    echo "<tr><td><strong>Abstract</strong></td><td colspan='3'>" . tmp_format_abstract($dataset->dats_abstract) . "</td></tr>";
    if (isset($dataset->dats_purpose)) {
        echo "<tr><td><strong>Observing strategy</strong></td><td colspan='3'>" . tmp_format_abstract($dataset->dats_purpose) . "</td></tr>";
    }
    echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
}

// add by Lolo
function editSiteDescr(&$dataset)
{
    echo '</td></tr><tr><th colspan="4" align="center"><strong>Site description</strong></th></tr>';
    echo "<tr><td><strong>Abstract</strong></td><td colspan='3'>" . tmp_format_abstract($dataset->dats_abstract) . "</td></tr>";
    echo "<tr><td><strong>Observing strategy</strong></td><td colspan='3'>" . tmp_format_abstract($dataset->dats_purpose) . "</td></tr>";
    echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
}

function editParameter(&$dats_var, $withPrecision = true, $withDates = true, $withLevelType = false)
{
    if (isset($dats_var->variable->var_name) && !empty($dats_var->variable->var_name)) {
        echo "<tr><td><strong>Parameter name</strong></td><td colspan='3'>" . $dats_var->variable->var_name . "</td></tr>";
    } else {
        echo "<tr><td><strong>Parameter name</strong></td><td colspan='3'>" . $dats_var->variable->gcmd->gcmd_name . "</td></tr>";
    }

    echo "<tr><td><strong>Parameter keyword</strong></td><td colspan='3'>" . printGcmdScience($dats_var->variable->gcmd) . "</td></tr>";
    echo "<tr><td><strong>Unit</strong></td><td colspan='3'>" . ((isset($dats_var->unit) && !empty($dats_var->unit)) ? $dats_var->unit->toString() : "") . "</td></tr>";
    echo "<tr><td><strong>Acquisition methodology and quality</strong></td><td colspan='3'>" . $dats_var->methode_acq . "</td></tr>";
    if ($withDates) {
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dats_var->date_min . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $dats_var->date_max . "</td></tr>";
    }
    if ($withPrecision) {
        echo "<tr><td><strong>Sensor precision / incertainty</strong></td><td colspan='3'>" . $dats_var->variable->sensor_precision . "</td></tr>";
    }
    if ($withLevelType) {
        echo "<tr><td><strong>Vertical level type</strong></td><td colspan='3'>" . $dats_var->level_type . "</td></tr>";
    }
}

//add by lolo
function editParameterFromSensorVar(&$sensor_var)
{
    if (isset($sensor_var->variable->var_name) && !empty($sensor_var->variable->var_name)) {
        echo "<tr><td><strong>Parameter name</strong></td><td colspan='3'>" . $sensor_var->variable->var_name . "</td></tr>";
    } else {
        echo "<tr><td><strong>Parameter name</strong></td><td colspan='3'>" . $sensor_var->variable->gcmd->gcmd_name . "</td></tr>";
    }

    echo "<tr><td><strong>Parameter keyword</strong></td><td colspan='3'>" . printGcmdScience($sensor_var->variable->gcmd) . "</td></tr>";
    echo "<tr><td><strong>Unit</strong></td><td colspan='3'>" . ((isset($sensor_var->unit) && !empty($sensor_var->unit)) ? $sensor_var->unit->toString() : "") . "</td></tr>";
    echo "<tr><td><strong>Acquisition methodology and quality</strong></td><td colspan='3'>" . $sensor_var->methode_acq . "</td></tr>";
    echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $sensor_var->date_min . "</td>";
    echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $sensor_var->date_max . "</td></tr>";
    echo "<tr><td><strong>Sensor precision / incertainty</strong></td><td colspan='3'>" . $sensor_var->sensor_precision . "</td></tr>";
}

function editDataUse(&$dataset, $withRequiredFormat = true)
{
    echo '</td></tr><tr><th colspan="4" align="center"><strong>Data use information</strong></th></tr>';
    echo "<tr><td><strong>Use constraints</strong></td><td colspan='3'>" . $dataset->dats_use_constraints . "</td></tr>";
    echo "<tr><td><strong>Data policy</strong></td><td colspan='3'>" . $dataset->data_policy->data_policy_name . "</td></tr>";
    echo "<tr><td><strong>Database</strong></td><td colspan='3'>" . $dataset->database->database_name . "</td></tr>";
    echo "<tr><td><strong>Original data format(s)</strong></td><td colspan='3'>";
    foreach ($dataset->data_formats as $format) {
        echo $format->data_format_name . "<br>";
    }
    if ($withRequiredFormat) {
        $labelReqFormat = 'Required data format(s)';
    } else {
        $labelReqFormat = 'Distribution data format(s)';
    }
    if (isset($dataset->required_data_formats) && !empty($dataset->required_data_formats)) {
        echo "<tr><td><strong>$labelReqFormat</strong></td><td colspan='3'>";
        foreach ($dataset->required_data_formats as $format) {
            echo $format->data_format_name . "<br>";
        }
    }
}

function editSiteBoundings(&$site)
{
    if ((isset($site->west_bounding_coord) && strlen($site->west_bounding_coord) > 0)
    || (isset($site->east_bounding_coord) && strlen($site->east_bounding_coord))
    || (isset($site->north_bounding_coord) && strlen($site->north_bounding_coord))
    || (isset($site->south_bounding_coord) && strlen($site->south_bounding_coord))
    ) {
        echo "<tr><td><strong>West bounding coordinate (°)</strong></td><td>" . $site->west_bounding_coord . "</td>";
        echo "<td><strong>East bounding coordinate (°)</strong></td><td>" . $site->east_bounding_coord . "</td></tr>";
        echo "<tr><td><strong>North bounding coordinate (°)</strong></td><td>" . $site->north_bounding_coord . "</td>";
        echo "<td><strong>South bounding coordinate (°)</strong></td><td>" . $site->south_bounding_coord . "</td></tr>";
    }
    if ((isset($site->place_elevation_min) && strlen($site->place_elevation_min) > 0)
    || (isset($site->place_elevation_max) && strlen($site->place_elevation_max) > 0)
    ) {
        echo "<tr><td><strong>Altitude min</strong></td><td>" . $site->place_elevation_min . "</td>";
        echo "<td><strong>Altitude max</strong></td><td>" . $site->place_elevation_max . "</td></tr>";
    }
}

function editGrid(&$ds)
{
    editSensorResolution($ds, true);
    echo '<tr><td colspan="4" align="center"><strong>Grid type</strong></td></tr>';
    echo "<tr><td><strong>Original Grid type</strong></td><td colspan='3'>" . $ds->grid_original . "</td></tr>";
    echo "<tr><td><strong>Required grid processing</strong></td><td colspan='3'>" . $ds->grid_process . "</td></tr>";
}

//Renvoie true si qqch a été écrit
function editSensorResolution(&$ds, $isGrid = false)
{
    if ($isGrid) {
        echo '<tr><td colspan="4" align="center"><strong>Data resolution</strong></td></tr>';
        echo "<tr><td><strong>Temporal resolution</strong></td><td colspan='3'>" . $ds->sensor_resol_temp . "</td></tr>";
        echo "<tr><td><strong>Latitude resolution</strong></td><td colspan='3'>" . $ds->sensor_lat_resolution . "</td></tr>";
        echo "<tr><td><strong>Longitude resolution</strong></td><td colspan='3'>" . $ds->sensor_lon_resolution . "</td></tr>";
        echo "<tr><td><strong>Vertical resolution</strong></td><td colspan='3'>" . $ds->sensor_vert_resolution . "</td></tr>";
        return true;
    } else {
        $infoTrouve = false;
        if (isset($ds->sensor_resol_temp)) {
            echo "<tr><td><strong>Observation frequency</strong></td><td colspan='3'>" . $ds->sensor_resol_temp . "</td></tr>";
            $infoTrouve = true;
        }
        if (isset($ds->sensor_lat_resolution)) {
            echo "<tr><td><strong>Horizontal coverage</strong></td><td colspan='3'>" . $ds->sensor_lat_resolution . "</td></tr>";
            $infoTrouve = true;
        }
        if (isset($ds->sensor_vert_resolution)) {
            echo "<tr><td><strong>Vertical coverage</strong></td><td colspan='3'>" . $ds->sensor_vert_resolution . "</td></tr>";
            $infoTrouve = true;
        }
        return $infoTrouve;
    }
}

function editSatelliteDataset(&$dataset, $project_name, $queryArgs = array())
{
    if (isset($dataset) && !empty($dataset)) {
        if ($project_name != strtolower(MainProject)) {
            $rubrique_cible = "/$project_name/Satellite-Data";
        } else {
            $rubrique_cible = "/portal/Satellite-Data";
        }

        if ($dataset->is_requested) {
            if ($project_name != strtolower(MainProject)) {
                $rubrique_cible = "/$project_name/Satellite-products-request";
            } else {
                $rubrique_cible = "/portal/Satellite-products-request";
            }
        }
        echo '<table><tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        editDOI($dataset->dats_doi);
        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        echo "<tr><td><strong>Version</strong></td><td colspan='3'>" . $dataset->dats_version . "</td></tr>";
        echo "<tr><td><strong>Useful in the framework of</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        echo "<tr><td><strong>Dataset Contact(s)</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';
        editDataAvailability($dataset, $project_name, $queryArgs);
        echo "<tr><td><strong>Purpose</strong></td><td colspan='3'>" . $dataset->dats_purpose . "</td></tr>";
        echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Instrument' . ((count($dataset->dats_sensors) > 1) ? 's' : '') . '</strong></th></tr>';
        for ($i = 0, $size = count($dataset->dats_sensors); $i < $size; $i++) {
            if (count($dataset->dats_sensors) > 1) {
                echo '<tr><td colspan="4" align="center"><strong>Instrument ' . ($i + 1) . '</strong></td></tr>';
            }

            $dataset->dats_sensors[$i]->sensor->get_sensor_places();
            echo "<tr><td><strong>Satellite</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_places[0]->place->place_name . "</td></tr>";
            echo "<tr><td><strong>Instrument</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_model . "</td></tr>";
            echo "<tr><td><strong>Instrument type</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->gcmd_instrument_keyword->gcmd_sensor_name . "</td></tr>";
            if (isset($dataset->dats_sensors[$i]->sensor->sensor_url) && !empty($dataset->dats_sensors[$i]->sensor->sensor_url)) {
                echo "<tr><td><strong>Reference</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_url . "</td></tr>";
            }
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Parameters</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if (count($dataset->dats_variables) > 1) {
                echo '<tr><td colspan="4" align="center"><strong>Parameter ' . ($cpt++) . '</strong></td></tr>';
            }
            editParameter($dats_var, false, false, true);
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Coverage</strong></th></tr>';
        echo '<tr><td colspan="4" align="center"><strong>Temporal Coverage</strong></td></tr>';
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_end . "</td></tr>";
        echo '<tr><td colspan="4" align="center"><strong>Geographic Coverage</strong></td></tr>';
        if (isset($dataset->sites) && isset($dataset->sites[0]) && !empty($dataset->sites[0])) {
            echo "<tr><td><strong>Area name</strong></td><td colspan='3'>" . $dataset->sites[0]->place_name . "</td></tr>";
            editSiteBoundings($dataset->sites[0]);
        }
        editGrid($dataset->dats_sensors[0]);
        editDataUse($dataset);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $rubrique_cible . "?datsId=" . $dataset->dats_id . "'\"/>";
        echo "</td></tr></table>";
    }
}

function editValueDataset(&$dataset, $project_name, $queryArgs = array())
{
    if ($project_name != strtolower(MainProject)) {
        $rubrique_cible = "/$project_name/Value-Added-Data";
    } else {
        $rubrique_cible = "/portal/Value-Added-Data";
    }

    if (isset($dataset) && !empty($dataset)) {
        echo '<table><tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        editDOI($dataset->dats_doi);
        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        echo "<tr><td><strong>Useful in the framework of</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        echo "<tr><td><strong>Dataset Contact</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';
        editDataAvailability($dataset, $project_name, $queryArgs);
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Data description</strong></th></tr>';
        echo "<tr><td><strong>Dataset description</strong></td><td colspan='3'>" . $dataset->dats_abstract . "</td></tr>";
        echo "<tr><td><strong>Purpose</strong></td><td colspan='3'>" . $dataset->dats_purpose . "</td></tr>";
        echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
        if (isset($dataset->attFile) && !empty($dataset->attFile)) {
            echo "<tr><td><strong>Attached document</strong></td><td colspan='3'>";
            echo "<a href='/downAttFile.php?file=" . $dataset->attFile . "' >" . $dataset->attFile . "</a>";
            echo "</td></tr>";
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Parameter' . ((count($dataset->dats_variables) > 1) ? 's' : '') . '</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if (count($dataset->dats_variables) > 1) {
                echo '<tr><td colspan="4" align="center"><strong>Parameter ' . ($cpt++) . '</strong></td></tr>';
            }
            editParameter($dats_var, false, false, true);
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Coverage</strong></th></tr>';
        echo '<tr><td colspan="4" align="center"><strong>Temporal Coverage</strong></td></tr>';
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_end . "</td></tr>";
        echo '<tr><td colspan="4" align="center"><strong>Geographic Coverage</strong></td></tr>';
        if (isset($dataset->sites) && isset($dataset->sites[0]) && !empty($dataset->sites[0])) {
            echo "<tr><td><strong>Area name</strong></td><td colspan='3'>" . $dataset->sites[0]->place_name . "</td></tr>";
            editSiteBoundings($dataset->sites[0]);
        }
        editGrid($dataset->dats_sensors[0]);
        editDataUse($dataset);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $rubrique_cible . "?datsId=" . $dataset->dats_id . "'\"/>";
        echo "</td></tr></table>";
    }
}

function editValueAddedDataset(&$dataset, $project_name, $queryArgs = array())
{
    if ($project_name != strtolower(MainProject)) {
        $rubrique_cible = "/$project_name/Value-Added-Dataset";
    } else {
        $rubrique_cible = "/portal/Value-Added-Dataset";
    }

    if (isset($dataset) && !empty($dataset)) {
        echo '<table><tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        if (isset($dataset->dats_doi) && !empty($dataset->dats_doi)) {
            echo "<tr><td><strong>Dataset DOI</strong></td><td colspan='3'>" . $dataset->dats_doi . "</td></tr>";
        }

        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        echo "<tr><td><strong>Useful in the framework of</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        echo "<tr><td><strong>Dataset Contact</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';
        editDataAvailability($dataset, $project_name, $queryArgs);
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Data description</strong></th></tr>';
        echo "<tr><td><strong>Dataset description</strong></td><td colspan='3'>" . $dataset->dats_abstract . "</td></tr>";
        echo "<tr><td><strong>Purpose</strong></td><td colspan='3'>" . $dataset->dats_purpose . "</td></tr>";
        echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
        if (isset($dataset->attFile) && !empty($dataset->attFile)) {
            echo "<tr><td><strong>Attached document</strong></td><td colspan='3'>";
            echo "<a href='/downAttFile.php?file=" . $dataset->attFile . "' >" . $dataset->attFile . "</a>";
            echo "</td></tr>";
        }
        echo '<tr><th colspan="4" align="center"><strong>Coverage</strong></th></tr>';
        echo '<tr><td colspan="4" align="center"><strong>Temporal Coverage</strong></td></tr>';
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_end . "</td></tr>";
        echo '<tr><td colspan="4" align="center"><strong>Geographic Coverage</strong></td></tr>';
        if (isset($dataset->sites)) {
            echo "<tr><td><strong>Area name</strong></td><td colspan='3'>" . $dataset->sites[0]->place_name . "</td></tr>";
            editSiteBoundings($dataset->sites[0]);
        }
        editGrid($dataset->dats_sensors[0]);
        echo '<tr><th colspan="4" align="center"><strong>Sources Information</strong></th></tr>';
        if ($dataset->nbModFormSensor > 0) {
            for ($i = $dataset->nbSatFormSensor + 1; $i < ($dataset->nbModFormSensor + $dataset->nbSatFormSensor + 1); $i++) {
                if ($dataset->nbModFormSensor > 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Model ' . ($i - $dataset->nbSatFormSensor) . '</strong></td></tr>';
                } else {
                    echo '<tr><td colspan="4" align="center"><strong>Model</strong></td></tr>';
                }

                if (isset($dataset->sites) && isset($dataset->sites[$i]) && !empty($dataset->sites[$i])) {
                    if (isset($dataset->sites[$i]->parent_place)) {
                        echo "<tr><td><strong>Type</strong></td><td colspan='3'>"
                        . $dataset->sites[$i]->parent_place->gcmd_plateform_keyword->gcmd_plat_name
                        . ' > ' . $dataset->sites[$i]->parent_place->place_name . "</td></tr>";
                    }
                    echo "<tr><td><strong>Model</strong></td><td colspan='3'>" . $dataset->sites[$i]->place_name . "</td></tr>";
                }
                echo "<tr><td><strong>Simulation</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_model . "</td></tr>";
                if (isset($dataset->dats_sensors[$i + 1]->sensor_resol_temp) && !empty($dataset->dats_sensors[$i + 1]->sensor_resol_temp)) {
                    echo "<tr><td><strong>Temporal Resolution</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor_resol_temp . "</td></tr>";
                }
            }
        }
        if ($dataset->nbSatFormSensor > 0) {
            for ($i = 0; $i < $dataset->nbSatFormSensor; $i++) {
                if ($dataset->nbSatFormSensor > 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Satellite ' . ($i + 1) . '</strong></td></tr>';
                } else {
                    echo '<tr><td colspan="4" align="center"><strong>Satellite</strong></td></tr>';
                }

                $dataset->dats_sensors[$i + 1]->sensor->get_sensor_places();
                echo "<tr><td><strong>Satellite</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i + 1]->sensor->sensor_places[0]->place->place_name . "</td></tr>";
                echo "<tr><td><strong>Instrument</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i + 1]->sensor->sensor_model . "</td></tr>";
                echo "<tr><td><strong>Instrument type</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i + 1]->sensor->gcmd_instrument_keyword->gcmd_sensor_name . "</td></tr>";
                if (isset($dataset->dats_sensors[$i + 1]->sensor_resol_temp) && !empty($dataset->dats_sensors[$i + 1]->sensor_resol_temp)) {
                    echo "<tr><td><strong>Temporal Resolution</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i + 1]->sensor_resol_temp . "</td></tr>";
                }

                if (isset($dataset->dats_sensors[$i + 1]->sensor->sensor_url) && !empty($dataset->dats_sensors[$i + 1]->sensor->sensor_url)) {
                    echo "<tr><td><strong>Reference</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i + 1]->sensor->sensor_url . "</td></tr>";
                }
            }
        }
        $ind = $dataset->nbModFormSensor + $dataset->nbSatFormSensor + 1;
        $nbsensors = $dataset->nbModFormSensor + $dataset->nbSatFormSensor + $dataset->nbInstruFormSensor + 1;
        if ($dataset->nbInstruFormSensor > 0) {
            for ($i = $ind; $i < $nbsensors; $i++) {
                if ($dataset->nbInstruFormSensor > 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Instrument ' . ($i - $ind + 1) . '</strong></td></tr>';
                } else {
                    echo '<tr><td colspan="4" align="center"><strong>Instrument</strong></td></tr>';
                }

                $dataset->dats_sensors[$i]->sensor->get_sensor_places();
                echo "<tr><td><strong>Instrument type</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->gcmd_instrument_keyword->gcmd_sensor_name . "</td></tr>";
                if (isset($dataset->dats_sensors[$i]->sensor->sensor_model) && !empty($dataset->dats_sensors[$i]->sensor->sensor_model)) {
                    echo "<tr><td><strong>Model</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_model . "</td></tr>";
                }

                if (isset($dataset->dats_sensors[$i]->sensor_resol_temp) && !empty($dataset->dats_sensors[$i]->sensor_resol_temp)) {
                    echo "<tr><td><strong>Temporal Resolution</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor_resol_temp . "</td></tr>";
                }

                if (isset($dataset->dats_sensors[$i]->sensor->sensor_url) && !empty($dataset->dats_sensors[$i]->sensor->sensor_url)) {
                    echo "<tr><td><strong>Reference</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_url . "</td></tr>";
                }

                if (isset($dataset->dats_sensors[$i]->sensor->sensor_environment) && !empty($dataset->dats_sensors[$i]->sensor->sensor_environment)) {
                    echo "<tr><td><strong>Instrument environment</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_environment . "</td></tr>";
                }

                if (isset($dataset->sites[$i]->place_name) && !empty($dataset->sites[$i]->place_name)) {
                    echo "<tr><td><strong>Location name</strong></td><td colspan='3'>" . $dataset->sites[$i]->place_name . "</td></tr>";
                }

                echo "<tr><td><strong>Plateform type</strong></td><td colspan='3'>" . $dataset->sites[$i]->gcmd_plateform_keyword->gcmd_plat_name . "</td></tr>";
            }
        }
        echo '<tr><th colspan="4" align="center"><strong>Parameter' . ((count($dataset->dats_variables) > 1) ? 's' : '') . '</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if (count($dataset->dats_variables) > 1) {
                echo '<tr><td colspan="4" align="center"><strong>Parameter ' . ($cpt++) . '</strong></td></tr>';
            }
            editParameter($dats_var, false, false, true);
        }
        editDataUse($dataset);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $rubrique_cible . "?datsId=" . $dataset->dats_id . "'\"/>";
        echo "</td></tr></table>";
    }
}

function editModelDataset(&$dataset, $project_name, $queryArgs = array())
{
    if ($project_name != strtolower(MainProject)) {
        $rubrique_cible = "/$project_name/Model-Data";
    } else {
        $rubrique_cible = "/portal/Model-Data";
    }

    if ($dataset->is_requested) {
        if ($project_name != strtolower(MainProject)) {
            $rubrique_cible = "/$project_name/Model-outputs-request";
        } else {
            $rubrique_cible = "/portal/Model-outputs-request";
        }
    }
    if (isset($dataset) && !empty($dataset)) {
        echo '<table><tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        editDOI($dataset->dats_doi);
        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        echo "<tr><td><strong>Useful in the framework of</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        echo "<tr><td><strong>Dataset Contact</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';
        editDataAvailability($dataset, $project_name, $queryArgs);
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Model information</strong></th></tr>';
        if (isset($dataset->sites) && isset($dataset->sites[1]) && !empty($dataset->sites[1])) {
            if (isset($dataset->sites[1]->parent_place)) {
                echo "<tr><td><strong>Type</strong></td><td colspan='3'>"
                . $dataset->sites[1]->parent_place->gcmd_plateform_keyword->gcmd_plat_name
                . ' > ' . $dataset->sites[1]->parent_place->place_name . "</td></tr>";
            }
            echo "<tr><td><strong>Model</strong></td><td colspan='3'>" . $dataset->sites[1]->place_name . "</td></tr>";
        }
        echo "<tr><td><strong>Simulation</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->sensor_model . "</td></tr>";
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Data description</strong></th></tr>';
        echo "<tr><td><strong>Model / simulation description</strong></td><td colspan='3'>" . $dataset->dats_abstract . "</td></tr>";
        echo "<tr><td><strong>Purpose</strong></td><td colspan='3'>" . $dataset->dats_purpose . "</td></tr>";
        echo "<tr><td><strong>References</strong></td><td colspan='3'>" . $dataset->dats_reference . "</td></tr>";
        if (isset($dataset->attFile) && !empty($dataset->attFile)) {
            echo "<tr><td><strong>Attached document</strong></td><td colspan='3'>";
            echo "<a href='/downAttFile.php?file=" . $dataset->attFile . "' >" . $dataset->attFile . "</a>";
            echo "</td></tr>";
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Parameter' . ((count($dataset->dats_variables) > 1) ? 's' : '') . '</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if (count($dataset->dats_variables) > 1) {
                echo '<tr><td colspan="4" align="center"><strong>Parameter ' . ($cpt++) . '</strong></td></tr>';
            }
            editParameter($dats_var, false, false, true);
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Coverage</strong></th></tr>';
        echo '<tr><td colspan="4" align="center"><strong>Temporal Coverage</strong></td></tr>';
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_end . "</td></tr>";
        echo '<tr><td colspan="4" align="center"><strong>Geographic Coverage</strong></td></tr>';
        if (isset($dataset->sites) && isset($dataset->sites[0]) && !empty($dataset->sites[0])) {
            echo "<tr><td><strong>Area name</strong></td><td colspan='3'>" . $dataset->sites[0]->place_name . "</td></tr>";
            editSiteBoundings($dataset->sites[0]);
        }
        editGrid($dataset->dats_sensors[0]);
        editDataUse($dataset);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $rubrique_cible . "?datsId=" . $dataset->dats_id . "'\"/>";
        echo "</td></tr></table>";
    }
}

function editDataset($datsId, $project_name, $display_archived = false)
{
    if (isset($datsId) && !empty($datsId)) {
        $dataset = new dataset();
        $dataset = $dataset->getById($datsId);
        if (isset($dataset) && !empty($dataset)) {
            if ($dataset->is_archived && !$display_archived) {
                echo "<span class='danger'><strong>This dataset has been archived. </strong></span>";
                return;
            }
            if ($display_archived) {
                // Historique archive
                $journal = new journal();
                $journal = $journal->getByDataset($dataset->dats_id, TYPE_ARCHIVE . ',' . TYPE_UNARCHIVE);
                if (isset($journal) && !empty($journal)) {
                    foreach ($journal as $jEntry) {
                        echo '<p>';
                        if ($jEntry->type_id == TYPE_ARCHIVE) {
                            echo '<span class="blue_tag">ARCHIVE ';
                        } elseif ($jEntry->type_id == TYPE_UNARCHIVE) {
                            echo '<span class="lightblue_tag">UNARCHIVE ';
                        }
                        echo $jEntry->date->format('Y-m-d') . '</span>';
                        if (isset($jEntry->comment) && !empty($jEntry->comment)) {
                                echo $jEntry->comment;
                        }
                        echo '</p>';
                    }
                }
            } else {
                echo "<a href='/sortie/fiche2pdf.php?datsId=$datsId' target='_blank'><img src='/img/pdf-icone-32.png' style='border:0px;float: right; margin-right:10px;' title='Export to pdf' /></a>";
            }
            if (get_class($dataset) == 'satellite_dataset') {
                $dataset->display($project_name);
            } elseif (get_class($dataset) == 'model_dataset') {
                $dataset->display($project_name);
            } elseif (get_class($dataset) == 'multi_instru_dataset') {
                $dataset->display($project_name);
            } elseif ($dataset->isValueAddedDataset()) {
                //  TODO à modifier quand le nouveau formulaire fonctionnera
                editValueAddedDataset($dataset, $project_name);
            } else {
                editInSituDataset($dataset, $project_name);
            }
        }
    }
}

function editInSituDatasetSite(&$dataset, $project_name, $queryArgs = array())
{
    if ($project_name != strtolower(MainProject)) {
        $rubrique_cible = "/$project_name/In-Situ-Site-Registration";
    } else {
        $rubrique_cible = "/portal/In-Situ-Site-Registration";
    }

    if (isset($dataset) && !empty($dataset)) {
        echo '<table><tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        editDOI($dataset->dats_doi);
        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        editSiteBoundings($dataset->sites[0]);
        echo "<tr><td><strong>Project(s)</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        echo "<tr><td><strong>Period</strong></td><td colspan='3'>" . $dataset->period->period_name . "</td></tr>";
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . (($dataset->dats_date_end_not_planned) ? 'not planned' : $dataset->dats_date_end) . "</td></tr>";

        echo "<tr><td><strong>Contacts</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';

        editDataAvailability($dataset, $project_name, $queryArgs);

        editSiteDescr($dataset);

        echo '</td></tr><tr><th colspan="4" align="center"><strong>Site information</strong></th></tr>';
        echo '<tr><td colspan="4" align="center"><strong>Site</strong></td></tr>';
        echo "<tr><td><strong>Site name</strong></td><td colspan='3'>" . $dataset->sites[0]->place_name . "</td></tr>";
        echo "<tr><td><strong>Plateform type</strong></td><td colspan='3'>" . $dataset->sites[0]->gcmd_plateform_keyword->gcmd_plat_name . "</td></tr>";
        if (isset($dataset->sites[0]->parent_place) && !empty($dataset->sites[0]->parent_place)) {
            echo "<tr><td><strong>Predefined site</strong></td><td colspan='3'>" . printPredefinedSite($dataset->sites[0]->parent_place) . "</td></tr>";
            echo "<tr><td><strong>Site type</strong></td><td colspan='3'>" . $dataset->sites[0]->parent_place->gcmd_plateform_keyword->gcmd_plat_name . "</td></tr>";
        }

        editSiteBoundings($dataset->sites[0]);

        $mapForm = new map_form();
        $url = new url();
        $map = $url->getMapFileByDataset($dataset->dats_id);
        $mapUrl = null;
        if (isset($map) && !empty($map)) {
            $mapUrl = $map[0]->url;
        }

        if ($mapForm->genScriptFromSite($dataset->sites[0], $mapUrl)) {
            echo '<tr><td colspan="4">';
            $mapForm->displayDrawLink('View site location on a map');
            $mapForm->displayMapDiv();
            echo '</td></tr>';
        }

        if (isset($dataset->image) && !empty($dataset->image)) {
            echo "<tr><td><strong>Photo</strong></td>";
            echo '<td><a href="' . $dataset->image . '" target=_blank><img src="' . $dataset->image . '" width="50" /></a></td><td colspan="2">';
        }

        echo '</td></tr><tr><th colspan="4" align="center"><strong>Instrument information</strong></th></tr>';
        for ($i = 0, $size = count($dataset->dats_sensors); $i < $size; $i++) {
            $nb = $i + 1;
            echo '</td></tr><tr><th colspan="4" align="center"><strong>Instrument ' . $nb . '</strong></th></tr>';
          //echo 'TEST';
            print_r($dataset->dats_sensors[0]->gcmd_instrument_keyword);
            echo "<tr><td><strong>Instrument type</strong></td><td colspan='3'>" . printGcmdInstrument($dataset->dats_sensors[0]->gcmd_instrument_keyword) . "</td></tr>";
            echo "<tr><td><strong>Manufacturer</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->manufacturer->manufacturer_name;
            if (isset($dataset->dats_sensors[$i]->sensor->manufacturer->manufacturer_url) && !empty($dataset->dats_sensors[$i]->sensor->manufacturer->manufacturer_url)) {
                echo " - <a href=\"" . $dataset->dats_sensors[$i]->sensor->manufacturer->manufacturer_url . "\" >" . $dataset->dats_sensors[$i]->sensor->manufacturer->manufacturer_url . "</a>";
            } else {
                echo "</td></tr>";
            }
            echo "<tr><td><strong>Model</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_model . "</td></tr>";
            echo "<tr><td><strong>Reference</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_url . "</td></tr>";
            echo "<tr><td><strong>Instrument features / Calibration</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_calibration . "</td></tr>";
            editSensorResolution($dataset->dats_sensors[$i]);
            echo "<tr><td><strong>Longitude (°)</strong></td><td>" . $dataset->dats_sensors[$i]->sensor->boundings->west_bounding_coord . "</td><td><strong>Latitude (°)</strong></td><td>" . $dataset->dats_sensors[$i]->sensor->boundings->north_bounding_coord . "</td></tr>";
            echo "<tr><td><strong>Sensor altitude (m)</strong></td><td>" . $dataset->dats_sensors[$i]->sensor->sensor_elevation . "</td>";
            echo "<td><strong>Height above ground (m)</strong></td><td>" . $dataset->dats_sensors[$i]->sensor->sensor_height . "</td></tr>";
            echo "<tr><td><strong>Instrument environment</strong></td><td colspan='3'>" . $dataset->dats_sensors[$i]->sensor->sensor_environment . "</td></tr>";

            $cpt = 1;
            foreach ($dataset->dats_sensors[$i]->sensor->sensor_vars as $sensor_var) {
                if ($sensor_var->flag_param_calcule != 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Instrument ' . $nb . ', parameter ' . ($cpt++) . '</strong></td></tr>';
                    editParameterFromSensorVar($sensor_var);
                }
            }
        }
        editDataUse($dataset, false);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $rubrique_cible . "?datsId=" . $dataset->dats_id . "&project_name=" . $project_name . "'\"/>";
        echo "</td></tr></table>";
    }
}

function editInSituDataset(&$dataset, $project_name, $queryArgs = array())
{
    if ($project_name != strtolower(MainProject)) {
        $rubrique_cible = "/$project_name/In-Situ-Instrument-Registration";
    } else {
        $rubrique_cible = "/portal/In-Situ-Instrument-Registration";
    }

    if (isset($dataset) && !empty($dataset)) {
        $url_cible = $rubrique_cible . '?datsId=' . $dataset->dats_id;
        echo '<table>';
        echo "</tr><th colspan=\"4\" align=\"right\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $url_cible . "'\"/></th></tr>";
        echo '<tr><th colspan="4" align="center"><strong>General information</strong></th></tr>';
        echo "<tr><td><strong>Dataset name</strong></td><td colspan='3'>" . $dataset->dats_title . "</td></tr>";
        editDOI($dataset->dats_doi);
        echo "<tr><td><strong>Created on</strong></td><td colspan='3'>" . $dataset->dats_pub_date . "</td></tr>";
        echo "<tr><td><strong>Project(s)</strong></td><td colspan='3'>";
        foreach ($dataset->projects as $proj) {
            echo $proj->toString() . "<br>";
        }
        echo "</td></tr>";
        if (isset($dataset->period)) {
            echo "<tr><td><strong>Period</strong></td><td colspan='3'>" . $dataset->period->period_name . "</td></tr>";
        }
        echo "<tr><td><strong>Date begin (yyyy-mm-jj)</strong></td><td>" . $dataset->dats_date_begin . "</td>";
        echo "<td><strong>Date end (yyyy-mm-jj)</strong></td><td>" . (($dataset->dats_date_end_not_planned) ? 'not planned' : $dataset->dats_date_end) . "</td></tr>";

        echo "<tr><td><strong>Contacts</strong></td><td colspan='3'>";
        editContact($dataset->dats_originators);
        echo '</td></tr>';
        editDataAvailability($dataset, $project_name, $queryArgs);
        editDataDescr($dataset);
        echo '</td></tr>';
        if (isset($dataset->attFile) && !empty($dataset->attFile)) {
            echo "<tr><td><strong>Attached document</strong></td><td colspan='3'>";
            echo "<a href='/downAttFile.php?file=" . $dataset->attFile . "' >" . $dataset->attFile . "</a>";
            echo "</td></tr>";
        }
        echo '<tr><th colspan="4" align="center"><strong>Instrument information</strong></th></tr>';
        $sectionVide = true;
        if (isset($dataset->dats_sensors[0]->sensor->gcmd_instrument_keyword)) {
            echo "<tr><td><strong>Instrument type</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->gcmd_instrument_keyword->gcmd_sensor_name . "</td></tr>";
            $sectionVide = false;
        }
        if (isset($dataset->dats_sensors[0]->sensor->manufacturer)) {
            echo "<tr><td><strong>Manufacturer</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->manufacturer->manufacturer_name;
            $sectionVide = false;
            if (isset($dataset->dats_sensors[0]->sensor->manufacturer->manufacturer_url) && !empty($dataset->dats_sensors[0]->sensor->manufacturer->manufacturer_url)) {
                echo " - <a href=\"" . $dataset->dats_sensors[0]->sensor->manufacturer->manufacturer_url . "\" >" . $dataset->dats_sensors[0]->sensor->manufacturer->manufacturer_url . "</a>";
            }
            echo "</td></tr>";
        }
        if (isset($dataset->dats_sensors[0]->sensor->sensor_model)) {
            echo "<tr><td><strong>Model</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->sensor_model . "</td></tr>";
            $sectionVide = false;
        }
        if (isset($dataset->dats_sensors[0]->sensor->sensor_url)) {
            echo "<tr><td><strong>Reference</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->sensor_url . "</td></tr>";
            $sectionVide = false;
        }
        if (isset($dataset->dats_sensors[0]->sensor->sensor_calibration)) {
            echo "<tr><td><strong>Instrument features / Calibration</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->sensor_calibration . "</td></tr>";
            $sectionVide = false;
        }
        if (editSensorResolution($dataset->dats_sensors[0], false)) {
            $sectionVide = false;
        }
        if (isset($dataset->dats_sensors[0]->sensor->boundings)) {
            echo "<tr><td><strong>Longitude (°)</strong></td><td>" . $dataset->dats_sensors[0]->sensor->boundings->west_bounding_coord . "</td><td><strong>Latitude (°)</strong></td><td>" . $dataset->dats_sensors[0]->sensor->boundings->north_bounding_coord . "</td></tr>";
            $sectionVide = false;
        }
        if (isset($dataset->dats_sensors[0]->sensor->sensor_elevation)) {
            echo "<tr><td><strong>Height above ground (m)</strong></td><td colspan='3'>" . $dataset->dats_sensors[0]->sensor->sensor_elevation . "</td></tr>";
            $sectionVide = false;
        }
        if (isset($dataset->image) && !empty($dataset->image)) {
            echo "<tr><td><strong>Photo</strong></td>";
            echo '<td><a href="' . $dataset->image . '" target=_blank><img src="' . $dataset->image . '" width="50" /></a></td><td colspan="2"></td></tr>';
            $sectionVide = false;
        }

        if ($sectionVide) {
            echo '<tr><td colspan="4"><em>No information available</em></td></tr>';
        }
      //Map
        $mapForm = new map_form();
        $url = new url();
        $map = $url->getMapFileByDataset($dataset->dats_id);
        if (!isset($map) || empty($map)) {
            if ($mapForm->genScriptFromSensor($dataset->dats_sensors[0]->sensor)) {
                echo '<tr><td colspan="4" id="map_cell" >';
                $mapForm->displayDrawLink('View instrument location on a map');
                $mapForm->displayMapDiv();
                echo '</td></tr>';
            }
        }
        echo '<tr><th colspan="4" align="center"><strong>Geographic information</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->sites as $site) {
            echo '<tr><td colspan="4" align="center"><strong>Location ' . ($cpt++) . '</strong></td></tr>';
            if (isset($site->parent_place) && !empty($site->parent_place)) {
                echo "<tr><td><strong>Predefined site</strong></td><td colspan='3'>" . printPredefinedSite($site->parent_place) . "</td></tr>";
                echo "<tr><td><strong>Site type</strong></td><td colspan='3'>" . $site->parent_place->gcmd_plateform_keyword->gcmd_plat_name . "</td></tr>";
            }
            if (isset($site->place_name) && !empty($site->place_name)) {
                echo "<tr><td><strong>Location name</strong></td><td colspan='3'>" . $site->place_name . "</td></tr>";
                echo "<tr><td><strong>Plateform type</strong></td><td colspan='3'>" . $site->gcmd_plateform_keyword->gcmd_plat_name . "</td></tr>";
            }
            editSiteBoundings($site);
            if (isset($site->sensor_environment) && !empty($site->sensor_environment)) {
                echo "<tr><td><strong>Instrument environment</strong></td><td colspan='3'>" . $site->sensor_environment . "</td></tr>";
            }
        }
        if (isset($map) && !empty($map)) {
            if ($mapForm->genScriptFromUrl($map[0]->url)) {
                echo '<tr><td colspan="4" id="map_cell" >';
                $mapForm->displayDrawLink('View stations location on a map');
                $mapForm->displayMapDiv();
                echo '</td></tr>';
            }
        } else {
        }
        echo '</td></tr><tr><th colspan="4" align="center"><strong>Measured parameter' . ((count($dataset->dats_variables) > 1) ? 's' : '') . '</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if ($dats_var->flag_param_calcule != 1) {
                if (count($dataset->dats_variables) > 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Measured parameter ' . ($cpt++) . '</strong></td></tr>';
                }
                editParameter($dats_var);
            }
        }
        echo '</td></tr>';
      //Cherche s'il y a des params dérivés
        foreach ($dataset->dats_variables as $dats_var) {
            if ($dats_var->flag_param_calcule == 1) {
            }
        }
        echo '<tr><th colspan="4" align="center"><strong>Derived parameter' . ((count($dataset->dats_variables) > 1) ? 's' : '') . '</strong></th></tr>';
        $cpt = 1;
        foreach ($dataset->dats_variables as $dats_var) {
            if ($dats_var->flag_param_calcule == 1) {
                if (count($dataset->dats_variables) > 1) {
                    echo '<tr><td colspan="4" align="center"><strong>Derived parameter ' . ($cpt++) . '</strong></td></tr>';
                }
                editParameter($dats_var);
            }
        }
        editDataUse($dataset, false);
        echo "</td></tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Update this dataset\" onclick=\"location.href='" . $url_cible . "'\"/>";
        echo "</td></tr></table>";
    }
}
