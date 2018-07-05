<?php

require_once "bd/bdConnect.php";

class data_availability
{
    public $ins_dats_id;
    public $var_id;
    public $place_id;

    public $date_begin;
    public $date_end;

    public $val_min;
    public $val_max;
    public $nb_valeurs;

    public $period;

    public function new_data_availability($tab)
    {
        $this->ins_dats_id = $tab[0];
        $this->var_id = $tab[1];
        $this->place_id = $tab[2];
        $this->date_begin = $tab[3];
        $this->date_end = $tab[4];
        $this->val_min = $tab[5];
        $this->val_max = $tab[6];
        $this->nb_valeurs = $tab[7];
        $this->period = $tab[8];
    }

    public function getByDatsVarPlace($ins_dats_id, $var_id, $place_id, $year = null)
    {
        if ($year) {
            $whereYear = "AND date_end >= '$year-01-01' and date_begin <= '$year-12-31'";
        } else {
            $whereYear = '';
        }

        $query = "SELECT * FROM data_availability"
        . " WHERE ins_dats_id = $ins_dats_id and var_id = $var_id and place_id = $place_id $whereYear order by date_begin;";
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new data_availability();
                $liste[$i]->new_data_availability($resultat[$i]);
            }
        }
        return $liste;
    }
}
