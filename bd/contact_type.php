<?php
/*
 * Created on 8 juil. 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "bd/bdConnect.php";

class contact_type
{
    public $contact_type_id;
    public $contact_type_name;

    public function new_contact_type($tab)
    {
        $this->contact_type_id = $tab[0];
        $this->contact_type_name = $tab[1];
    }

    public function getAll()
    {
        $query = 'select * from contact_type order by contact_type_id';
        $bd = new bdConnect();
        $liste = array();
        if ($resultat = $bd->get_data($query)) {
            for ($i = 0, $size = count($resultat); $i < $size; $i++) {
                $liste[$i] = new contact_type();
                $liste[$i]->new_contact_type($resultat[$i]);
            }
        }
        return $liste;
    }

    public function getById($id)
    {
        if (!isset($id) || empty($id)) {
            return new contact_type();
        }

        $query = "select * from contact_type where contact_type_id = " . $id;

        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $contact_type = new contact_type();
            $contact_type->new_contact_type($resultat[0]);
        }
        return $contact_type;
    }

    public function getByName($name)
    {
        $query = "select * from contact_type where lower(contact_type_name) = '" . strtolower($name) . "'";
        $contact_type = null;
        $bd = new bdConnect();
        if ($resultat = $bd->get_data($query)) {
            $contact_type = new contact_type();
            $contact_type->new_contact_type($resultat[0]);
        }
        return $contact_type;
    }

  //creer element select pour formulaire
    public function chargeForm($form, $label, $titre)
    {

        $liste = $this->getAll();
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->contact_type_id;
            $array[$j] = $liste[$i]->contact_type_name;
        }

        $s = &$form->createElement('select', $label, $titre);
        $s->loadArray($array);
        return $s;
    }
}
