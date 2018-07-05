<?php
/*
 * Created on 12 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";

class event
{
    public $event_id;
    public $event_name;
    public $event_date_begin;
    public $event_date_end;

    public function new_event($tab)
    {
        $this->event_id = $tab[0];
        $this->event_name = $tab[1];
        $this->event_date_begin = $tab[2];
        $this->event_date_end = $tab[3];
    }

    public function getAll()
    {
        $query = "select * from event order by event_date_begin";
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new event();
                $liste[$i]->new_event($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new event();
        }

        $query = "select * from event where event_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $per = new event();
            $per->new_event($resultat[0]);
        }
        return $per;
    }
}
