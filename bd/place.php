<?php
/*
 * AM, Modif 17 septembre 2018 : suppression place_level, pla_place_id, enfants, parent_place à la table
 */
require_once "bd/bdConnect.php";
require_once "bd/conf.php";
require_once "bd/gcmd_plateform_keyword.php";
require_once "bd/boundings.php";
require_once "scripts/common.php";
require_once ("bd/gcmd_location_keyword.php");

class place
{
    public $place_id;
    public $bound_id;
    public $gcmd_plat_id;
    public $place_name;
    public $place_elevation_min;
    public $place_elevation_max;
    public $boundings;
    public $gcmd_plateform_keyword;
    public $gcmd_location_keyword;
    public $gcmd_loc_id;
    public $west_bounding_coord;
    public $east_bounding_coord;
    public $north_bounding_coord;
    public $south_bounding_coord;
    public $sensor_environment;

    public function new_place($tab)
    {
        $this->place_id = $tab[0];
        $this->bound_id = $tab[1];
        $this->gcmd_plat_id = $tab[2];
        $this->place_name = $tab[3];
        $this->place_elevation_min = intAlt2double($tab[4]);
        $this->place_elevation_max = intAlt2double($tab[5]);
        $this->wmo_code = $tab[6];
		$this->gcmd_loc_id = $tab[7];
        
        if (isset($this->bound_id) && !empty($this->bound_id)) {
            $bound = new boundings();
            $this->boundings = $bound->getById($this->bound_id);

            $this->west_bounding_coord = &$this->boundings->west_bounding_coord;
            $this->east_bounding_coord = &$this->boundings->east_bounding_coord;
            $this->north_bounding_coord = &$this->boundings->north_bounding_coord;
            $this->south_bounding_coord = &$this->boundings->south_bounding_coord;
        }
        if (isset($this->gcmd_plat_id) && !empty($this->gcmd_plat_id)) {
            $gcmd = new gcmd_plateform_keyword();
            $this->gcmd_plateform_keyword = $gcmd->getById($this->gcmd_plat_id);
        }
    }

    public function toString()
    {
        $result = 'Site: ' . (($this->gcmd_plateform_keyword) ? $this->gcmd_plateform_keyword->gcmd_plat_name . ' > ' : '') . $this->place_name;

        if (isset($this->boundings)) {
            $result .= "\nBoundings: " . $this->boundings->toString();
        }

        if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0) {
            $result .= "\nAltitude min: " . $this->place_elevation_min;
        }
        if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0) {
            $result .= "\nAltitude max: " . $this->place_elevation_max;
        }
        return $result;
    }

    public function getAll()
    {
        $query = "select * from place order by place_name";
        return $this->getByQuery($query);
    }

    public function getByLevel($level = 1, $parent = 0, $type = 0)
    {
        $where = "where place_level = $level";

        if ($parent > 0) {
            $where .= " and pla_place_id = $parent";
        }
        if ($type > 0) {
            $where .= " and gcmd_plat_id = $type ";
        }

        $query = "select * from place $where order by place_name";

        return $this->getByQuery($query);
    }

    public function getAllInSitu()
    {
        $query = "select * from place_insitu order by place_name";
        return $this->getByQuery($query);
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new place();
        }

        $query = "select * from place where place_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $place = new place();
            $place->new_place($resultat[0]);
        }
        return $place;
    }

    public function getPlaceNameById($id)
    {
        $query = "select place_name from place where place_id = " . $id;
        $bd = new bdConnect();
        $resultat = $bd->get_data($query);
        return $resultat;
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new place();
                $liste[$i]->new_place($resultat[$i]);
            }
        }
        return $liste;
    }

    public function existeComplet()
    {
        $where = "where lower(place_name) = lower('" . (str_replace("'", "\'", $this->place_name)) . "')";

        if (isset($this->gcmd_plat_id) && !empty($this->gcmd_plat_id)) {
            $where .= " and gcmd_plat_id = " . $this->gcmd_plat_id;
        }
        if (isset($this->bound_id) && !empty($this->bound_id) && $this->bound_id != -1) {
            $where .= " and bound_id = " . $this->bound_id;
        }
        if (isset($this->pla_place_id) && !empty($this->pla_place_id)) {
            $where .= " and pla_place_id = " . $this->pla_place_id;
        }
        if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0) {
            $where .= " and place_elevation_min = " . doubleAlt2int($this->place_elevation_min);
        }
        if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0) {
            $where .= " and place_elevation_max = " . doubleAlt2int($this->place_elevation_max);
        }
        if (isset($this->place_level) && !empty($this->place_level)) {
            $where .= " and place_level = " . $this->place_level;
        }

        $query = "select * from place $where";

        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_place($resultat[0]);
            return true;
        }
        return false;
    }

    public function existe()
    {
        $query = "select * from place where " .
        "lower(place_name) = lower('" . (str_replace("'", "\'", $this->place_name)) . "')";

        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_place($resultat[0]);
            return true;
        }
        return false;
    }

    public function idExiste()
    {
        $query = "select * from place where place_id = " . $this->place_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_place($resultat[0]);
            return true;
        }
        return false;
    }

    public function insert(&$bd)
    {
        if (isset($this->boundings) && $this->bound_id != -1) {
            $this->boundings->insert($bd);
            $this->bound_id = $this->boundings->bound_id;
        }

        if (!$this->existeComplet()) {
            $query_insert = "insert into place (place_name";
            $query_values = "values ('" . str_replace("'", "\'", $this->place_name) . "'";
            if (isset($this->gcmd_plateform_keyword) && $this->gcmd_plat_id > 0) {
                $query_insert .= ",gcmd_plat_id";
                $query_values .= "," . $this->gcmd_plat_id;
            }
            if (isset($this->bound_id) && !empty($this->bound_id) && $this->bound_id != -1) {
                $query_insert .= ",bound_id";
                $query_values .= "," . $this->bound_id;
            }
            if (isset($this->pla_place_id) && !empty($this->pla_place_id)) {
                $query_insert .= ",pla_place_id";
                $query_values .= "," . $this->pla_place_id;
            }
            if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0) {
                $query_insert .= ",place_elevation_min";
                $query_values .= "," . doubleAlt2int($this->place_elevation_min);
            }
            if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0) {
                $query_insert .= ",place_elevation_max";
                $query_values .= "," . doubleAlt2int($this->place_elevation_max);
            }
            $query = $query_insert . ") " . $query_values . ")";

            $bd->exec($query);

            $this->place_id = $bd->getLastId("place_place_id_seq");
        }
        return $this->place_id;
    }

    public function chargeFormModelCategsNew($form, $label, $titre)
    {
        $gcmd = new gcmd_plateform_keyword();
        $type = $gcmd->getByName(model_dataset::GCMD_CATEG);
    
        $lev1 = $this->getByLevel(1, 0, $type->gcmd_plat_id);
        foreach ($lev1 as $item1) {
            $array_lev1[$item1->place_id] = $item1->place_name;
            $lev2 = $this->getByLevel(2, $item1->place_id, $type->gcmd_plat_id);
            foreach ($lev2 as $item2) {
                $array_lev2[$item1->place_id][$item2->place_id] = $item2->place_name;
            }
        }
        $s = &$form->createElement('hierselect', $label, $titre, null, '');
        $s->setOptions(array($array_lev1, $array_lev2));
        return $s;
    }

  //Encore utilisé par va dataset
  //TODO remplacer par le nouveau
    public function chargeFormModelCategs($form, $label, $titre)
    {
        $gcmd = new gcmd_plateform_keyword();
        $types = $gcmd->getByIds(MODEL_CATEGORIES);
    
        foreach ($types as $type) {
            $array_type[$type->gcmd_plat_id] = $type->gcmd_plat_name;
            $liste = $this->getByLevel(1, 0, $type->gcmd_plat_id);
            foreach ($liste as $item) {
                $array_stype[$type->gcmd_plat_id][$item->place_id] = $item->place_name;
            }
        }
        $s = &$form->createElement('hierselect', $label, $titre, null, '');
        $s->setOptions(array($array_type, $array_stype));
        return $s;
    }

    public function chargeFormSiteLevels($form, $label, $titre)
    {
        global $project_name;
        $array_type[0] = "";
        $array_lev1[0][0] = "";
        $array_lev2[0][0][0] = "";
        $array_lev3[0][0][0][0] = "";

        $gcmd = new gcmd_plateform_keyword();
        if (constant(strtoupper($project_name) . '_SITES') != '' && constant(strtoupper($project_name) . '_SITES') != null) {
            $types = $gcmd->getByIds(constant(strtoupper($project_name) . '_SITES'));
            foreach ($types as $type) {
                $array_type[$type->gcmd_plat_id] = $type->gcmd_plat_name;
                $liste1 = $this->getByLevel(1, 0, $type->gcmd_plat_id);
                foreach ($liste1 as $site1) {
                    $array_lev1[$type->gcmd_plat_id][$site1->place_id] = $site1->place_name;
                    $array_lev2[$type->gcmd_plat_id][$site1->place_id][0] = '';
                    $liste2 = $this->getByLevel(2, $site1->place_id);
                    foreach ($liste2 as $site2) {
                        $array_lev2[$type->gcmd_plat_id][$site1->place_id][$site2->place_id] = $site2->place_name;
                        $array_lev3[$type->gcmd_plat_id][$site1->place_id][$site2->place_id][0] = '';
                        $liste3 = $this->getByLevel(3, $site2->place_id);
                        foreach ($liste3 as $site3) {
                            $array_lev3[$type->gcmd_plat_id][$site1->place_id][$site2->place_id][$site3->place_id] = $site3->place_name;
                        }
                    }
                }
            }
            $s = &$form->createElement('hierselect', $label, $titre, null, '<br>');
            $s->setOptions(array(
            $array_type,
            $array_lev1,
            $array_lev2,
            $array_lev3,
            ));
            return $s;
        }
    }

  //creer element select pour formulaire
    public function chargeForm($form, $label, $titre, $indice)
    {
        $liste = $this->getAllInSitu();

        $array[0] = "";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->place_id;
            $array[$j] = $liste[$i]->place_name;
        }

        if (isset($indice)) {
            $boxesNames = "['new_place_" . $indice . "','place_alt_min_" . $indice . "','west_bound_" . $indice . "','east_bound_" . $indice . "','north_bound_" . $indice . "','south_bound_" . $indice . "','place_alt_max_" . $indice . "','gcmd_plat_key_" . $indice . "']";
            $columnsNames = "['place_name','place_elevation_min','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_max','gcmd_plat_id']";
        } else {
            $boxesNames = "['new_place','place_alt_min','west_bound','east_bound','north_bound','south_bound','place_alt_max','gcmd_plat_key']";
            $columnsNames = "['place_name','place_elevation_min','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_max','gcmd_plat_id']";
        }
        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => "fillBoxes('" . $label . "'," . $boxesNames . ",'place'," . $columnsNames . ");", 'style' => 'width: 200px;'));
        return $s;
    }

    public function chargeFormModNew($form, $label, $titre)
    {
        return $this->chargeFormByType($form, $label, $titre, model_dataset::GCMD_CATEG, "updateMod();");
    }

  //Encore utilisé par va dataset
  //TODO remplacer par le nouveau
    public function chargeFormMod($form, $label, $titre, $onchange = "updateMod();")
    {
        $query = 'SELECT DISTINCT ON (place_name) * from place where gcmd_plat_id in (' . GCMD_PLAT_MODEL . ') AND place_level IS NULL order by place_name';
        $liste = $this->getByQuery($query);
        $array[0] = "";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->place_id;
            $array[$j] = $liste[$i]->place_name;
        }

        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => $onchange));
        return $s;
    }

    public function chargeFormInstruvadataset($form, $label = "instru_place_", $titre = "instru_place")
    {
        $liste = $this->getAllInSitu();
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->place_id;
            $array[$j] = $liste[$i]->place_name;
        }

        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => $onchange));

        return $s;
    }

    public function chargeFormSatCategs($form, $label, $titre)
    {
        $gcmd = new gcmd_plateform_keyword();
        $type = $gcmd->getByName("Satellites");

        $liste = $this->getByLevel(1, 0, $type->gcmd_plat_id);
        foreach ($liste as $item) {
            $array[$item->place_id] = $item->place_name;
        }

        $s = &$form->createElement('select', $label, $titre, $array);

        return $s;
    }

    public function chargeFormSat($form, $i, $label = 'satellite_', $titre = 'Satellite')
    {
        return $this->chargeFormByType($form, $label . $i, $titre, 'Satellites', 'updateSat(' . $i . ');');
    }

    public function chargeFormRegion($form, $label, $titre, $simpleVersion = false)
    {
        if ($simpleVersion) {
            $boxesNames = "['new_area','west_bound_0','east_bound_0','north_bound_0','south_bound_0']";
            $columnsNames = "['place_name','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord']";
        } else {
            $boxesNames = "['new_area','west_bound_0','east_bound_0','north_bound_0','south_bound_0','place_alt_min_0','place_alt_max_0']";
            $columnsNames = "['place_name','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_min','place_elevation_max']";
        }
        return $this->chargeFormByType($form, $label, $titre, "Geographic Regions", "fillBoxes('" . $label . "'," . $boxesNames . ",'place'," . $columnsNames . ");");
    }

    public function chargeFormByType($form, $label, $titre, $type, $onchange)
    {
        $query = "select * from place where gcmd_plat_id in (select gcmd_plat_id from gcmd_plateform_keyword where gcmd_plat_name ilike '%" . $type . "%') AND place_level IS NULL order by place_name";
        $liste = $this->getByQuery($query);
        $array[0] = "";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->place_id;
            $array[$j] = $liste[$i]->place_name;
        }

        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => $onchange, 'onclick' => $onchange));

        return $s;
    }

    public function chargeFormByTypeVadataset($form, $label, $titre, $type, $onchange)
    {
        $query = "select * from place where gcmd_plat_id in (select gcmd_plat_id from gcmd_plateform_keyword where gcmd_plat_name ilike '%" . $type . "%') AND place_level IS NULL order by place_name";
        $liste = $this->getByQuery($query);
        $x = 0;
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->place_id;
            $array[$j] = $liste[$i]->place_name;
            if (i == 0) {
                $x = $j;
            }
        }

        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => $onchange, 'onclick' => $onchange, 'onload' => $onchange));

        return $s;
    }
}
