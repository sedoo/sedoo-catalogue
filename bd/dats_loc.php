<?php
require_once "bd/bdConnect.php";
require_once "bd/dataset.php";
require_once "bd/gcmd_location_keyword.php";

class dats_loc
{

    public $dats_id;
    public $gcmd_loc_id;
    public $dataset;
    public $gcmd_location_keyword;

    public function new_dats_loc($tab)
    {
        $this->dats_id = $tab[0];
        $this->gcmd_loc_id = $tab[1];

        if (isset($this->dats_id) && !empty($this->dats_id)) {
            $dts = new dataset();
            $this->dataset = $dts->getById($this->dats_id);
        }
        if (isset($this->gcmd_loc_id) && !empty($this->gcmd_loc_id)) {
            $loc = new gcmd_location_keyword();
            $this->gcmd_location_keyword = $loc->getById($this->gcmd_loc_id);
        }
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new dats_loc();
                $liste[$i]->new_dats_loc($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getAll()
    {
        $query = "select * from dats_loc order by dats_id";
        return $this->getByQuery($query);
    }

    public function existe()
    {
        $query = "select * from dats_loc where " . "dats_id = " . $this->dats_id . " and gcmd_loc_id = " . $this->gcmd_loc_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_dats_loc($resultat[0]);
            return true;
        }
        return false;
    }

    public function insert(&$bd)
    {
        $query = "insert into dats_loc (dats_id,gcmd_loc_id) " . "values (" . $this->dats_id . "," . $this->gcmd_loc_id . ")";
        $bd->exec($query);
    }
}
