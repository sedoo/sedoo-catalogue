<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";
require_once "bd/variable.php";
require_once "bd/sensor.php";

class sensor_var
{
    public $var_id;
    public $sensor_id;
    public $sensor_precision;
    public $variable;
    public $sensor;
  //add by lolo
    public $unit;
    public $methode_acq;
    public $date_min;
    public $date_max;
    public $flag_param_calcule;

    public function new_sensor_var($tab)
    {
        $this->sensor_id = $tab[0];
        $this->var_id = $tab[1];
        $this->sensor_precision = $tab[2];
        $this->methode_acq = $tab[3];

        if (isset($this->sensor_id) && !empty($this->sensor_id)) {
            $sensor = new sensor();
            $this->sensor = $sensor->getById($this->sensor_id);
        }
        if (isset($this->var_id) && !empty($this->var_id)) {
            $var = new variable();
            $this->variable = $var->getById($this->var_id);
        }
    }

    public function getAll()
    {
        $query = "select * from sensor_var";
        return $this->getByQuery($query);
    }

    public function getByIds($v_id, $s_id)
    {
        $query = "select * from sensor_var where var_id = " . $v_id . " and sensor_id = " . $s_id;
        $liste = $this->getByQuery($query);
        if ($liste) {
            return $liste[0];
        } else {
            return null;
        }
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new sensor_var();
                $liste[$i]->new_sensor_var($resultat[$i]);
            }
        }
        return $liste;
    }

    public function existe()
    {
        $query = "select * from sensor_var where " .
        "sensor_id = " . $this->sensor_id . " and var_id = " . $this->var_id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $this->new_sensor_var($resultat[0]);
            return true;
        }
        return false;
    }

  //modif by lolo
    public function insert(&$bd)
    {
        if (isset($this->sensor_id) && isset($this->var_id) && !$this->existe()) {
            $query_insert = "insert into sensor_var (sensor_id,var_id";
            $query_values = "values (" . $this->sensor_id . "," . $this->var_id;

            if (!isset($this->sensor_precision) || empty($this->sensor_precision)) {
                $this->sensor_precision = ' ';
            }
            $query_insert .= ",sensor_precision";
            $query_values .= ",'" . str_replace("'", "\'", $this->sensor_precision) . "'";

            if (isset($this->methode_acq) && !empty($this->methode_acq)) {
                $query_insert .= ",methode_acq";
                $query_values .= ",'" . str_replace("'", "\'", $this->methode_acq) . "'";
            }

            $query = $query_insert . ") " . $query_values . ")";

            $bd->exec($query);
        } elseif ($this->existe()) {
            if (isset($this->sensor_precision)) {
                $query = "update sensor_var set sensor_precision = '" . $this->sensor_precision . "' where sensor_id = " . $this->sensor_id . " and var_id = " . $this->var_id;
                $bd->exec($query);
            }
            if (isset($this->methode_acq)) {
                $query = "update sensor_var set methode_acq = '" . $this->methode_acq . "' where sensor_id = " . $this->sensor_id . " and var_id = " . $this->var_id;
                $bd->exec($query);
            }
        }
    }

  //add by lolo
    public function getVariable()
    {
        $var = new variable();
        return $var->getById($this->var_id);
    }
}
