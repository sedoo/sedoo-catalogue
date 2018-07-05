<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";

class status_progress
{
    public $status_progress_id;
    public $status_progress_name;

    public function new_status_progress($tab)
    {
        $this->status_progress_id = $tab[0];
        $this->status_progress_name = $tab[1];
    }

    public function getAll()
    {
        $query = "select * from status_progress order by status_progress_name";
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new status_progress();
                $liste[$i]->new_status_progress($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new status_progress();
        }

        $query = "select * from status_progress where status_progress_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $status_progress = new status_progress();
            $status_progress->new_status_progress($resultat[0]);
        }
        return $status_progress;
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new status_progress();
                $liste[$i]->new_status_progress($resultat[$i]);
            }
        }
        return $liste;
    }

    public function existe()
    {
        $query = "select * from status_progress where " .
        "lower(status_progress_name) = lower('" . (str_replace("'", "\'", $this->status_progress_name)) . "')";
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->status_progress_id = $resultat[0][0];
            return true;
        }
        return false;
    }

    public function idExiste()
    {
        $query = "select * from status_progress where status_progress_id = " . $this->status_progress_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->status_progress_name = $resultat[0][1];
            return true;
        }
        return false;
    }

    public function insert()
    {
        $query = "insert into status_progress ('status_progress_name') " .
        "values ('" . str_replace("'", "\'", $this->status_progress_name) . "')";
        $bd = new bdConnect();
        $this->status_progress_id = $bd->insert($query);
    }

  //creer element select pour formulaire
    public function chargeForm($form, $label, $titre)
    {

        $liste = $this->getAll();
        $array[0] = "";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->status_progress_id;
            $array[$j] = $liste[$i]->status_progress_name;
        }
        $s = &$form->createElement('select', $label, $titre);
        $s->loadArray($array);
        return $s;
    }
}
