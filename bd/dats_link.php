<?php
require_once("bd/bdConnect.php");
require_once ("bd/type_link.php");
require_once ("bd/dataset.php");

class dats_link {
	
	var $dats_id;
	var $dats_dats_id;
	var $type_id;
	var $type_link;
	var $dataset;
	
	function new_dats_link($tab){
		$this->dats_id = $tab[0];
		$this->dats_dats_id = $tab[1];
		$this->type_id = $tab[2];
		
		if(isset($this->dats_id) && !empty($this->dats_id)){
			$dts = new dataset();
			$this->dataset = $dts->getById($this->dats_id);
		}
		if (isset($this->type_id) && !empty($this->type_id)){
			$type = new type_link();
			$this->type_link = $type->getById($this->type_id);
		}
	}
	
	function getByQuery($query) {
		$bd = new bdConnect ();
		$liste = array ();
		if ($resultat = $bd->get_data ( $query )) {
			for($i = 0; $i < count ( $resultat ); $i ++) {
				$liste [$i] = new dats_link ();
				$liste [$i]->new_dats_link ( $resultat [$i] );
			}
		}
		return $liste;
	}
	
	function getAll(){
		$query = "select * from dats_link order by dats_id";
		return $this->getByQuery($query);
	}
	
	
	function existe() {
		$query = "select * from dats_link where " . "dats_id = " . $this->dats_id . " and type_id = " . $this->type_id;
		$bd = new bdConnect ();
		if ($resultat = $bd->get_data ( $query )) {
			$this->new_dats_loc ( $resultat [0] );
			return true;
		}
		return false;
	}
	
	function insert(& $bd) {
		$query = "insert into dats_link (dats_id,type_id) " . "values (" . $this->dats_id . "," . $this->type_id . ")";
		$bd->exec ( $query );
	}
}

?>
