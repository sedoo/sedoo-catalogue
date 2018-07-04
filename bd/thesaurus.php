<?php
require_once "bd/bdConnect.php";

class thesaurus
{
    public $thesaurus_id;
    public $name;
    public $url;

    public function new_thesaurus($tab)
    {
        $this->thesaurus_id = $tab[0];
        $this->name = $tab[1];
        $this->url = $tab[2];
    }

    public function getByQuery($query)
    {
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0; $i < count($resultat); $i++) {
                $liste[$i] = new thesaurus();
                $liste[$i]->new_thesaurus($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new place();
        }

        $query = "select * from thesaurus where thesaurus_id = " . $id;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $thesaurus = new thesaurus();
            $thesaurus->new_thesaurus($resultat[0]);
        }
        return thesaurus;
    }

    public function getAll()
    {
        $query = "select * from thesaurus order by nom";
        return $this->getByQuery($query);
    }

    public function insert(&$bd)
    {
        $query = "insert into thesaurus (name,url) " .
        "values ('" . str_replace("'", "\'", $this->name) . "'" .
        ",'" . str_replace("'", "\'", $this->url) . "')";

        $bd->exec($query);

        $this->thesaurus_id = $bd->getLastId('thesaurus_thesaurus_id_seq');

        return $this->thesaurus_id;
    }
}
