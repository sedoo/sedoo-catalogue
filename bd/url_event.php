<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";

class url_event
{

    public $url_event_id;
    public $event_id;
    public $url_event;
    public $url_descript;
    public $event;

    public function new_url_event($tab)
    {
        $this->url_event_id = $tab[0];
        $this->event_id = $tab[1];
        $this->url_event = $tab[2];
        $this->url_descript = $tab[3];
        if (isset($this->event_id) && !empty($this->event_id)) {
            $dts = new event();
            $this->event = $dts->getById($this->event_id);
        }
    }

    public function getAll()
    {
        $query = "select * from url_event order by event_id";
        return $this->getByQuery($query);
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new url_event();
        }

        $query = "select * from url_event where url_event_id = $id";

        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $per = new url_event();
            $per->new_url_event($resultat[0]);
        }
        return $per;
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new url_event();
                $liste[$i]->new_url_event($resultat[$i]);
            }
        }
        return $liste;
    }
}
