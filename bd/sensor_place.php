<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";
require_once "bd/place.php";
require_once "bd/sensor.php";

class sensor_place
{
    public $place_id;
    public $sensor_id;
    public $environment;
    public $place;
    public $sensor;

    public function new_sensor_place($tab)
    {
        $this->sensor_id = $tab[0];
        $this->place_id = $tab[1];
        $this->environment = $tab[2];
    }

    public function getPlace()
    {
        if (isset($this->place_id) && !empty($this->place_id)) {
            $place = new place();
            $this->place = $place->getById($this->place_id);
        }
    }

    public function getSensor()
    {
        if (isset($this->sensor_id) && !empty($this->sensor_id)) {
            $sensor = new sensor();
            $this->sensor = $sensor->getById($this->sensor_id);
        }
    }

    public function getAll()
    {
        $query = "select * from sensor_place";
        return $this->getByQuery($query);
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new sensor_place();
                $liste[$i]->new_sensor_place($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getByIds($p_id, $s_id)
    {
        $query = "select * from sensor_place where place_id = " . $p_id . " and sensor_id = " . $s_id;
        $liste = $this->getByQuery($query);
        if ($liste) {
            return $liste[0];
        } else {
            return null;
        }
    }

    public function existe()
    {
        $query = "select * from sensor_place where " .
        "sensor_id = " . $this->sensor_id . " and place_id = " . $this->place_id;
      //echo $query."<br>";
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_sensor_place($resultat[0]);
            return true;
        }
        return false;
    }

    public function insert(&$bd)
    {
        if (!$this->existe()) {
            $query_insert = "insert into sensor_place (sensor_id,place_id";
            $query_values = "values (" . $this->sensor_id . "," . $this->place_id;

            if (isset($this->environment) && !empty($this->environment)) {
                $query_insert .= ",environment";
                $query_values .= ",'" . str_replace("'", "\'", $this->environment) . "'";
            }
            $query = $query_insert . ") " . $query_values . ")";

            $bd->exec($query);
        }
    }

    public function updateByDatsPlaceID($dats_id, $old_place_id, $place_id)
    {
        $query_update = "update sensor_place set place_id = '.$place_id.' where sensor_id in (select sensor_id from sensor left join dats_sensor using (sensor_id) where dats_id = '.$dats_id.' and place_id='.$old_place_id.')";
        $bd = new bdConnect();
        $bd->exec($query_update);
    }
}
