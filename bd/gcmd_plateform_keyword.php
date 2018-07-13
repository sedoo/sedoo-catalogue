<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";
require_once "bd/thesaurus.php";

class gcmd_plateform_keyword
{
    public $gcmd_plat_id;
    public $gcmd_plat_name;
    public $gcmd_level;
    public $gcm_gcmd_id;
    public $thesaurus_id;
    public $thesaurus;
    public $uid;

    public $gcmd_parent;
    public $enfants;

    public function new_gcmd_plateform_keyword($tab)
    {
        $this->gcmd_plat_id = $tab[0];
        $this->gcmd_plat_name = $tab[1];
        $this->gcmd_level = $tab[2];
        $this->gcm_gcmd_id = $tab[3];
        $this->thesaurus_id = $tab[4];
        $this->uid = $tab[5];

        if (isset($this->gcm_gcmd_id) && !empty($this->gcm_gcmd_id)) {
            $this->gcmd_parent = $this->getById($this->gcm_gcmd_id);
        }

        if (isset($this->thesaurus_id) && !empty($this->thesaurus_id)) {
            $thesaurus = new thesaurus();
            $this->thesaurus = $thesaurus->getById($this->thesaurus_id);
        }
    }

    public function getAll()
    {
        $query = "select * from gcmd_plateform_keyword order by gcmd_plat_name";
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new gcmd_plateform_keyword();
                $liste[$i]->new_gcmd_plateform_keyword($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getChildren($recursive = false)
    {
        $liste = array();
        $this->readChildren($liste, $recursive);
        return $liste;
    }

    private function readChildren(&$liste, $recursive = false)
    {
        $query = "SELECT * FROM gcmd_plateform_keyword WHERE gcm_gcmd_id = $this->gcmd_id ORDER BY gcmd_loc_name";
        $tmp = $this->getByQuery($query);
        if ($recursive && isset($tmp)) {
            foreach ($tmp as $child) {
                $liste[] = $child;
                $child->readChildren($liste, $recursive);
            }
        }
    }

    public function getAllInSitu()
    {
        $query = 'select * from gcmd_plateform_keyword where gcmd_plat_id not in (' . GCMD_PLAT_EXCLUDE_INSITU . ') order by gcmd_plat_name';
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new gcmd_plateform_keyword();
                $liste[$i]->new_gcmd_plateform_keyword($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new gcmd_plateform_keyword();
        }

        $query = "select * from gcmd_plateform_keyword where gcmd_plat_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $gcmd_plateform_keyword = new gcmd_plateform_keyword();
            $gcmd_plateform_keyword->new_gcmd_plateform_keyword($resultat[0]);
        }
        return $gcmd_plateform_keyword;
    }

    public function getByIds($ids)
    {
        if (!isset($ids) || empty($ids)) {
            return new gcmd_plateform_keyword();
        }

        $query = "select * from gcmd_plateform_keyword where gcmd_plat_id in ($ids) order by gcmd_plat_name";

        return $this->getByQuery($query);
    }

    public function getByName($name)
    {
        $query = "select * from gcmd_plateform_keyword where gcmd_plat_name = '" . $name . "'";
        $gcmd_plateform_keyword = null;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $gcmd_plateform_keyword = new gcmd_plateform_keyword();
            $gcmd_plateform_keyword->new_gcmd_plateform_keyword($resultat[0]);
        }
        return $gcmd_plateform_keyword;
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new gcmd_plateform_keyword();
                $liste[$i]->new_gcmd_plateform_keyword($resultat[$i]);
            }
        }
        return $liste;
    }

    public function existe()
    {
        $query = "select * from gcmd_plateform_keyword where " .
        "lower(gcmd_plat_name) = lower('" . (str_replace("'", "\'", $this->gcmd_plat_name)) . "')";
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->gcmd_plat_id = $resultat[0][0];
            return true;
        }
        return false;
    }

    public function idExiste()
    {
        $query = "select * from gcmd_plateform_keyword where gcmd_plat_id = " . $this->gcmd_plat_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->gcmd_plat_name = $resultat[0][1];
            return true;
        }
        return false;
    }

    public function insert()
    {
        $query = "insert into gcmd_plateform_keyword ('gcmd_plat_name') " .
        "values ('" . str_replace("'", "\'", $this->gcmd_plat_name) . "')";
        $bd = new bdConnect();
        $this->gcmd_plat_id = $bd->insert($query);
    }

    public function chargeForm($form, $label, $titre)
    {

        $liste = $this->getAllInSitu();
        $array[0] = "";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->gcmd_plat_id;
            $array[$j] = $liste[$i]->gcmd_plat_name;
        }
        $s = &$form->createElement('select', $label, $titre, $array, array('style' => 'width:200px;'));
        return $s;
    }

    public function chargeFormvadataset($form, $label, $titre)
    {

        $liste = $this->getAllInSitu();
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->gcmd_plat_id;
            $array[$j] = $liste[$i]->gcmd_plat_name;
        }
        $s = &$form->createElement('select', $label, $titre, $array, array('style' => 'width:200px;'));
        return $s;
    }

    public function chargeFormPlat($form, $label, $titre)
    {
        $array_topic[0] = "-- Topic --";
        $array_categorie[0][0] = "-- Term --";
        $array_variable[0][0][0] = "-- Var_level1 --";
        $query = "select * from gcmd_plateform_keyword where gcm_gcmd_id is null order by gcmd_plat_name";
        $liste_topic = $this->getByQuery($query);

        for ($i = 0, $size = count($liste_topic); $i < $size; $i++) {
            $j = $liste_topic[$i]->gcmd_plat_id;

            $array_topic[$j] = $liste_topic[$i]->gcmd_plat_name;

            $query2 = "select * from gcmd_plateform_keyword where gcm_gcmd_id = " . $j . " order by gcmd_plat_name";
            $liste_categ = $this->getByQuery($query2);
            $array_categorie[$j][0] = "-- Term --";

            for ($k = 0, $size2 = count($liste_categ); $k < $size2; $k++) {
                $l = $liste_categ[$k]->gcmd_plat_id;
                $array_categorie[$j][$l] = $liste_categ[$k]->gcmd_plat_name;

                $query3 = "select * from gcmd_plateform_keyword where gcm_gcmd_id = " . $l . " order by gcmd_plat_name";
                $liste_param = $this->getByQuery($query3);
                $array_variable[$j][$l][0] = "-- Var_level1 --";
                for ($m = 0, $size3 = count($liste_param); $m < $size3; $m++) {
                    $n = $liste_param[$m]->gcmd_plat_id;
                    $array_variable[$j][$l][$n] = $liste_param[$m]->gcmd_plat_name;
                }
            }
        }

        $s = &$form->createElement('hierselect', $label, $titre);
        $s->setOptions(array(
        $array_topic,
        $array_categorie,
        $array_variable,
        ));
        return $s;
    }
}
