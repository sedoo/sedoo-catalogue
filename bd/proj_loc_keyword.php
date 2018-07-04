<?php
require_once "bd/bdConnect.php";
require_once "bd/project.php";
require_once "bd/gcmd_location_keyword.php";

class proj_loc_keyword
{

    public $gcmd_loc_id;
    public $project_id;
    public $project;
    public $gcmd_location_keyword;

    public function new_proj_loc_keyword($tab)
    {
        $this->gcmd_loc_id = $tab[0];
        $this->project_id = $tab[1];

        if (isset($this->gcmd_loc_id) && !empty($this->gcmd_loc_id)) {
            $loc = new gcmd_location_keyword();
            $this->gcmd_location_keyword = $loc->getById($this->gcmd_loc_id);
        }

        if (isset($this->project_id) && !empty($this->project_id)) {
            $proj = new project();
            $this->project = $proj->getById($this->project_id);
        }
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0; $i < count($resultat); $i++) {
                $liste[$i] = new proj_loc_keyword();
                $liste[$i]->new_proj_loc_keyword($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getAll()
    {
        $query = "select * from proj_loc_keyword order by project_id";
        return $this->getByQuery($query);
    }

    public function existe()
    {
        $query = "select * from proj_loc_keyword where " . "gcmd_loc_id = " . $this->gcmd_loc_id . " and project_id = " . $this->project_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_dats_loc($resultat[0]);
            return true;
        }
        return false;
    }

    public function insert(&$bd)
    {
        $query = "insert into proj_loc_keyword (gcmd_loc_id,project_id) " . "values (" . $this->gcmd_loc_id . "," . $this->project_id . ")";
        $bd->exec($query);
    }
}
