<?php
/*
 * Created on 15 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once "bd/bdConnect.php";

class licence
{
    public $licence_id;
    public $licence_name;
    public $licence_url;
    public $licence_description;

    public function new_licence($tab)
    {
        $this->licence_id = $tab[0];
        $this->licence_name = $tab[1];
        $this->licence_url = $tab[2];
	$this->licence_description[3];
    }

    public function toString()
    {
        return $this->licence_name . (($this->licence_url) ? ',url: ' . $this->licence_url : '');
    }

    public function insert(&$bd)
    {
        if (!$this->existe()) {
            $query_insert = "INSERT INTO licence (licence_name";
            $query_values = "VALUES ('" . str_replace("'", "\'", $this->licence_name) . "'";

            if (isset($this->licence_url) && !empty($this->licence_url)) {
                $query_insert .= ",licence_url";
                $query_values .= ",'" . $this->licence_url . "'";
            }

            $query = $query_insert . ") " . $query_values . ")";

            $bd->exec($query);
            $this->licence_id = $bd->getLastId("licence_licence_id_seq");
        }
        return $this->licence_id;
    }

    public function getAll()
    {
        $query = "select * from licence order by licence_name";
        return $this->getByQuery($query);
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new licence();
        }

        $query = "SELECT * FROM licence WHERE licence_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $per = new licence();
            $per->new_licence($resultat[0]);
        }
        return $per;
    }


    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new database();
                $liste[$i]->new_database($resultat[$i]);
            }
        }
        return $liste;
    }

    public function existe()
    {
        $query = "select * from licence where " .
        "lower(licence_name) = lower('" . str_replace("'", "\'", $this->licence_name) . "') and " .
        "lower(licence_url) = lower(" . str_replace("'", "\'", $this->licence_url) . ")";
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_organism($resultat[0]);
            return true;
        }
        return false;
    }


    //creer element select pour formulaire
    public function chargeForm($form, $label, $titre, $indice)
    {

        $liste = $this->getAll();
        $array[0] = null;
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->org_id;

            if (isset($liste[$i]->licence_name) && !empty($liste[$i]->licence_name)) {
                $array[$j] = $liste[$i]->licence_name;
            } else {
                $array[$j] = $liste[$i]->org_fname;
            }
        }

        $boxesNames = "['licence_name" . $indice . "','licence_url" . $indice . "']";
        $columnsNames = "['org_sname',org_url']";

        $s = &$form->createElement('select', $label, $titre, $array, array('onchange' => "fillBoxes('" . $label . "'," . $boxesNames . ",'licence'," . $columnsNames . ");"));

        return $s;
    }

    

  
}
