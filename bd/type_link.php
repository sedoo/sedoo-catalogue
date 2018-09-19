<?php
require_once("bd/bdConnect.php");


class type_link {
	
	var $type_id;
	var $type_name;
	
	function new_type_name($tab){
		$this->type_id = $tab[0];
		$this->type_name = $tab[1];
	}
	
	function getByQuery($query) {
		$bd = new bdConnect ();
		$liste = array ();
		if ($resultat = $bd->get_data ( $query )) {
			for($i = 0; $i < count ( $resultat ); $i ++) {
				$liste [$i] = new type_link ();
				$liste [$i]->new_type_link ( $resultat [$i] );
			}
		}
		return $liste;
	}
	
	function getAll(){
		$query = "select * from type_link order by type_id";
		return $this->getByQuery($query);
	}
	
	
	function existe() {
		$query = "select * from type_link where " . "type_id = " . $this->type_id . " and type_name = " . $this->type_name;
		$bd = new bdConnect ();
		if ($resultat = $bd->get_data ( $query )) {
			$this->new_type_loc ( $resultat [0] );
			return true;
		}
		return false;
	}
	
	function insert(& $bd) {
		$query = "insert into type_link (type_id,type_name) " . "values (" . $this->type_id . "," . $this->type_name . ")";
		$bd->exec ( $query );
	}
}

?>
