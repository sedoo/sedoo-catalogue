<?php

require_once "forms/base_form.php";
require_once "conf/conf.php";
require_once 'scripts/filtreProjets.php';
require_once "bd/dataset.php";
require_once "bd/bdConnect.php";

class duplicate_dataset_form extends base_form
{

    public $duplicated_dats_id;
    public $duplicated_dats_title;

    public function createForm()
    {
        $dts = new dataset();
        $liste = $dts->getOnlyTitles("select dats_id,dats_title from dataset order by dats_title");
        $array[0] = "-- Datasets list --";
        for ($i = 0, $size = count($liste); $i < $size; $i++) {
            $j = $liste[$i]->dats_id;
            $array[$j] = $liste[$i]->dats_title;
        }
        $this->addElement('select', 'dataset', "Dataset", $array);
        $this->addElement('text', 'title', 'Duplicated dataset Title');
        $this->applyFilter('title', 'trim');
        $this->addRule('title', 'Duplicated dataset title is required', 'required');
        $this->addElement('submit', 'bouton_duplicate', 'duplicate');
    }

    public function displayForm()
    {
        echo '<h1>Duplicate dataset</h1>';
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                echo '<span class="danger">' . $error . '</span><br>';
            }
        }
        echo '<form action="" method="post" id="frmjnl" name="frmjnl" >';
        echo '<table>';
        echo '<tr><td><strong>' . $this->getElement('dataset')->getLabel() . '</strong></td><td>' . $this->getElement('dataset')->toHTML() . '</td></tr>';
        echo '<tr><td><strong>' . $this->getElement('title')->getLabel() . '</strong></td><td>' . $this->getElement('title')->toHTML() . '</td></tr>';
        echo '<tr><td colspan="2" align="center">' . $this->getElement('bouton_duplicate')->toHTML() . '</td></tr>';
        echo '</table>';
        echo '</form>';
    }

    public function duplicate_dataset()
    {
        $this->duplicated_dats_title = $this->exportValue('title');
        $bd = new bdConnect();
        $query = "SELECT duplicate_dataset_multi(" . $this->exportValue('dataset') . ",'" . $this->exportValue('title') . "');";
        $res = $bd->get_data($query);
        $this->duplicated_dats_id = $res[0][0];
        if (isset($this->duplicated_dats_id) && !empty($this->duplicated_dats_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function reset_form()
    {
        unset($this->duplicated_dats_title);
        unset($this->duplicated_dats_id);
    }

    public function get_id()
    {
        return $this->duplicated_dats_id;
    }

    public function get_title()
    {
        return $this->duplicated_dats_title;
    }
}
