<?php

require_once "scripts/logger.php";
require_once "conf.php";

/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class bdConnect
{
    public $hote = DB_HOST;
    public $db_name = DB_NAME;
    public $db_user = DB_USER;
    public $db_password = DB_PASS;
    public $conn;

    public function db_open()
    {
        if (!$this->conn = pg_pconnect("host=" . $this->hote . " user=" . $this->db_user . " dbname=" . $this->db_name . " password=" . $this->db_password)) {
            echo "Cannot connect to database.\n";
            exit;
        }
    }

    public function db_close()
    {
        if (!@pg_close($this->conn)) {
            echo "Erreur de fermeture de la base !!!";
        }
    }

    public function get_data($requete)
    {
        $this->db_open();
        if ($res = pg_query($this->conn, $requete)) {
            for ($i = 0; $i < pg_num_rows($res); $i++) {
                $tab_res[$i] = pg_fetch_row($res);
            }
        }
        $this->db_close();
        if (!isset($tab_res) || empty($tab_res)) {
            return null;
        }

        return $tab_res;
    }

    public function getLastId($sequence)
    {
        $query = "SELECT last_value from " . $sequence;
        $res = $this->exec($query);
        $id = pg_fetch_array($res);
        return $id[0];
    }

    public function insert($requete)
    {
        $this->exec($requete);
    }

    public function update($requete)
    {
        return $this->exec($requete);
    }

    public function exec($requete)
    {
        if (!isset($this->conn) || empty($this->conn)) {
            throw new Exception('ERREUR: Connection à la base non ouverte');
        }

        log_debug('SQL - ' . $requete);
        $res = pg_query($this->conn, $requete);
        if (!$res) {
            throw new Exception('Erreur SQL !' . $requete . '-' . pg_last_error($this->conn));
        }

        return $res;
    }

    public function beginTransaction()
    {
        return $this->exec("BEGIN");
    }

    public function commitTransaction()
    {
        return $this->exec("COMMIT");
    }
    public function rollbackTransaction()
    {
        return $this->exec("ROLLBACK");
    }
}
