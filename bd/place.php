<?php
/*
 * AM, Modif 17 aout 2018 : suppression place_level, pla_place_id, enfants, parent_place à la table
 */
 	require_once("bd/bdConnect.php");
	require_once("bd/conf.php");
 	require_once("bd/gcmd_plateform_keyword.php");
 	require_once("bd/boundings.php");
 	require_once("scripts/common.php");
 	require_once ("bd/gcmd_location_keyword.php");
 	
 	class place
 	{
 		var $place_id;
 		var $bound_id;
 		var $gcmd_plat_id;
 		var $place_name;
 		var $place_elevation_min;
 		var $place_elevation_max;
 		var $boundings;
 		var $gcmd_plateform_keyword;
 		
 		var $gcmd_location_keyword;
 		var $gcmd_loc_id;
 		
 		
 		var $west_bounding_coord;
 		var $east_bounding_coord;
 		var $north_bounding_coord;
 		var $south_bounding_coord;
 		 		
 		var $sensor_environment;
 		
 		function new_place($tab)
 		{
 			$this->place_id = $tab[0];
 			$this->bound_id = $tab[1];
 			$this->gcmd_plat_id = $tab[2];
 			$this->place_name = $tab[3];
 			$this->place_elevation_min = intAlt2double($tab[4]);
 			$this->place_elevation_max = intAlt2double($tab[5]);
 			$this->wmo_code = $tab[6];
 			$this->gcmd_loc_id = $tab[7];
 			
 			
 			if (isset($this->bound_id) && !empty($this->bound_id))
 			{
 				$bound = new boundings;
 				$this->boundings = $bound->getById($this->bound_id);
 				
 				$this->west_bounding_coord = & $this->boundings->west_bounding_coord;
 				$this->east_bounding_coord = & $this->boundings->east_bounding_coord;
 				$this->north_bounding_coord = & $this->boundings->north_bounding_coord;
 				$this->south_bounding_coord = & $this->boundings->south_bounding_coord;
 				
 				
 			}
 			if (isset($this->gcmd_plat_id) && !empty($this->gcmd_plat_id))
 			{
 				$gcmd = new gcmd_plateform_keyword;
 				$this->gcmd_plateform_keyword = $gcmd->getById($this->gcmd_plat_id);
 			}
 			
 			if (isset($this->gcmd_loc_id) && !empty($this->gcmd_loc_id)){
 				$location = new gcmd_location_keyword;
 				$this->gcmd_location_keyword = $location->getById($this->gcmd_loc_id);
 			}
 			
 		}
 		
 		function toString(){
 			$result = 'Site: '.(($this->gcmd_plateform_keyword)?$this->gcmd_plateform_keyword->gcmd_plat_name.' > ':'').$this->place_name;
 			
 			if (isset($this->boundings)){
     	 		$result .= "\nBoundings: ".$this->boundings->toString();
     	 	}
     	 	
     	 	if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0){
     	 		$result .= "\nAltitude min: ".$this->place_elevation_min;
     	 	}
     	 	if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0){
     	 		$result .= "\nAltitude max: ".$this->place_elevation_max;
     	 	}
 			return $result;
 			
 		}
 		
 		function getAll()
 		{
 			$query = "select * from place order by place_name";
      		return $this->getByQuery($query);
 		}

 		 		
 		
 		function getById($id)
    	{
      		if (!isset($id) || empty($id))
        		return new place;

      		$query = "select * from place where place_id = ".$id;
      		$bd = new bdConnect;
      		if ($resultat = $bd->get_data($query))
      		{
        		$place = new place;
        		$place->new_place($resultat[0]);
      		}
      		return $place;
    	}
    	
    	function getPlaceNameById($id){
    		$query = "select place_name from place where place_id = ".$id;
    		$bd = new bdConnect;
    		$resultat = $bd->get_data($query);
    		return $resultat;
    	}
    	
    	function getByQuery($query)
    	{
    		    		
      		$bd = new bdConnect;
      		$liste = array();
      		if ($resultat = $bd->get_data($query))
      		{
        		for ($i=0; $i<count($resultat);$i++)
        		{
          			$liste[$i] = new place;
          			$liste[$i]->new_place($resultat[$i]);
        		}
      		}
      		//print_r($liste);
      		return $liste;
    	}

    	function existeComplet(){    		
    		$where = "where lower(place_name) = lower('".(str_replace("'","''",$this->place_name))."')";
    		
    		if (isset($this->gcmd_plat_id) && !empty($this->gcmd_plat_id)) {
    			$where .= " and gcmd_plat_id = ". $this->gcmd_plat_id;    			
    		}
    		if (isset($this->bound_id) && !empty($this->bound_id) && $this->bound_id != -1) {
    			$where .= " and bound_id = ". $this->bound_id;    			
    		}
    		if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0){
    			$where .= " and place_elevation_min = ". doubleAlt2int($this->place_elevation_min);;    			
    		}
    		if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0){
    			$where .= " and place_elevation_max = ". doubleAlt2int($this->place_elevation_max);    			
    		}
		if (isset($this->wmo_code) && !empty($this->wmo_code)) {
    			$where .= " and wmo_code = ". $this->wmo_code;    			
    		}
    		if (isset($this->gcmd_loc_id) && !empty($this->gcmd_loc_id)) {
    			$where .= " and gcmd_loc_id = ". $this->gcmd_loc_id;
    		}
    		    		
    		$query = "select * from place $where";
    		
    		//echo $query.'<br>';
    		
    		$bd = new bdConnect;
        	if ($resultat = $bd->get_data($query))
        	{
          		$this->new_place($resultat[0]);
          		return true;
        	}
        	return false;
    	}
    	
    	function existe()
    	{
        	
        	$query = "select * from place where ".
        			"lower(place_name) = lower('".(str_replace("'","''",$this->place_name))."')";
        	
        	//echo $query."<br>";
        	$bd = new bdConnect;
        	if ($resultat = $bd->get_data($query))
        	{
          		$this->new_place($resultat[0]);
          		return true;
        	}
        	return false;
    	}

    	function idExiste()
    	{
        	$query = "select * from place where place_id = ".$this->place_id;
        	//echo $query."<br>";
        	$bd = new bdConnect;
        	if ($resultat = $bd->get_data($query))
        	{
          		$this->new_place($resultat[0]);
          		return true;
        	}
        	return false;
    	}
    	
    	function insert(& $bd)
    	{
    		if (isset($this->boundings) && $this->bound_id != -1){
	    			$this->boundings->insert($bd);
	    			$this->bound_id = $this->boundings->bound_id;
	    			//echo 'bound_id:'.$this->bound_id.'<br>';
	    		}
    		
    		//if (!$this->existe())
    		if (!$this->existeComplet())
    		{	    	    			    		
	     	 	$query_insert = "insert into place (place_name";
	     	 	$query_values = "values ('".str_replace("'","''",$this->place_name)."'";
	     	 	if (isset($this->gcmd_plateform_keyword) && $this->gcmd_plat_id > 0)
	     	 	{
	     	 		$query_insert .= ",gcmd_plat_id";
	     	 		$query_values .= ",".$this->gcmd_plat_id;
	     	 	}
	     	 	if (isset($this->bound_id) && !empty($this->bound_id) && $this->bound_id != -1)
	     	 	{
	     	 		$query_insert .= ",bound_id";
	     	 		$query_values .= ",".$this->bound_id;
	     	 	}
	     	 	if (isset($this->place_elevation_min) && strlen($this->place_elevation_min) > 0)
	     	 	{
	     	 		$query_insert .= ",place_elevation_min";
	     	 		$query_values .= ",".doubleAlt2int($this->place_elevation_min);
	     	 	}
	     	 	if (isset($this->place_elevation_max) && strlen($this->place_elevation_max) > 0)
	     	 	{
	     	 		$query_insert .= ",place_elevation_max";
	     	 		$query_values .= ",".doubleAlt2int($this->place_elevation_max);
	     	 	}
			if (isset($this->wmo_code) && !empty($this->wmo_code))
	     	 	{
	     	 		$query_insert .= ",wmo_code";
	     	 		$query_values .= ",".$this->wmo_code;
	     	 	}
	     	 	if (isset($this->gcmd_location_keyword) && strlen($this->gcmd_loc_id) > 0)
	     	 	{
	     	 		$query_insert .= ",gcmd_loc_id";
	     	 		$query_values .= ",".$this->gcmd_loc_id;
	     	 	}
	     	 	$query = $query_insert.") ".$query_values.")";
	     		
	      		$bd->exec($query);
	    		
	      		$this->place_id = $bd->getLastId("fplace_place_id_seq");
    		}
      		return $this->place_id;
    	}
    	
    	
    	//creer element select pour formulaire
    	function chargeForm($form,$label,$titre,$indice)
    	{

      		//$liste = $this->getAll();
      		$liste = $this->getAllInSitu();
      		    		    		
      		$array[0] = "";
      		for ($i = 0; $i < count($liste); $i++)
        	{
          		$j = $liste[$i]->place_id;
          		$array[$j] = $liste[$i]->place_name;
          		//echo 'array['.$j.'] = '.$array[$j].'<br>';
        	}
        	
        	if (isset($indice)){
        		$boxesNames = "['new_place_".$indice."','place_alt_min_".$indice."','west_bound_".$indice."','east_bound_".$indice."','north_bound_".$indice."','south_bound_".$indice."','place_alt_max_".$indice."','gcmd_plat_key_".$indice."']";
        		$columnsNames = "['place_name','place_elevation_min','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_max','gcmd_plat_id']";
        	}else{
        		$boxesNames = "['new_place','place_alt_min','west_bound','east_bound','north_bound','south_bound','place_alt_max','gcmd_plat_key']";
        		$columnsNames = "['place_name','place_elevation_min','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_max','gcmd_plat_id']";
        	}
        	$s = & $form->createElement('select',$label,$titre,$array,array('onchange' => "fillBoxes('".$label."',".$boxesNames.",'place',".$columnsNames.");",'style' => 'width: 200px;'));
 
        	/*
      		$s = & $form->createElement('select',$label,$titre);
      		$s->loadArray($array);*/
      		return $s;
    	}
    	
 	function chargeFormModOld($form,$label,$titre){
    		return $this->chargeFormByType($form,$label,$titre,"Model","updateMod();");
    	}

	function chargeFormMod($form,$label,$titre,$onchange = "updateMod();"){
		$query = 'SELECT DISTINCT ON (place_name) * from place where gcmd_plat_id in ('.GCMD_PLAT_MODEL.')  order by place_name';
                $liste = $this->getByQuery($query);
                $array[0] = "";
                for ($i = 0; $i < count($liste); $i++)
                {
                        $j = $liste[$i]->place_id;
                        $array[$j] = $liste[$i]->place_name;
                }

                $s = & $form->createElement('select',$label,$titre,$array,array('onchange' => $onchange));

                return $s;
	}
	
	function chargeFormInstruvadataset($form,$label="instru_place_",$titre="instru_place"){
		$liste = $this->getAllInSitu();
		//$array[0] = "";
		for ($i = 0; $i < count($liste); $i++)
		{
		$j = $liste[$i]->place_id;
			$array[$j] = $liste[$i]->place_name;
		}
	
		$s = & $form->createElement('select',$label,$titre,$array,array('onchange' => $onchange));
	
		return $s;
	}
	
	
	function chargeFormSat($form,$i,$label = 'satellite_',$titre = 'Satellite'){
		return $this->chargeFormByType($form,$label.$i,$titre,satellite_dataset::GCMD_PLATFORM_KEYWORD,'updateSat('.$i.');');
	}
	
    	function chargeFormSatvadataset($form,$i,$label = 'satellite_',$titre = 'Satellite'){
    		return $this->chargeFormByType($form,$label.$i,$titre,satellite_dataset::GCMD_PLATFORM_KEYWORD,'updateSat('.$i.');');
    	}

    	function chargeFormRegion($form,$label,$titre,$simpleVersion = false){
    		if ($simpleVersion){
    			$boxesNames = "['new_area','west_bound_0','east_bound_0','north_bound_0','south_bound_0']";
    			$columnsNames = "['place_name','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord']";
    		}else{
    			$boxesNames = "['new_area','west_bound_0','east_bound_0','north_bound_0','south_bound_0','place_alt_min_0','place_alt_max_0']";
        		$columnsNames = "['place_name','west_bounding_coord','east_bounding_coord','north_bounding_coord','south_bounding_coord','place_elevation_min','place_elevation_max']";
    		}
    		return $this->chargeFormByType($form,$label,$titre,base_dataset::GCMD_GEO_COVERAGE,"fillBoxes('".$label."',".$boxesNames.",'place',".$columnsNames.");");
    	}
    	
    	function chargeFormByType($form,$label,$titre,$type,$onchange){
    		$query = "select * from place where gcmd_plat_id in (select gcmd_plat_id from gcmd_plateform_keyword where gcmd_plat_name ilike '%".$type."%') order by place_name";
    		//echo 'place.chargeFormByType: '.$query;
    		$liste = $this->getByQuery($query);
    		$array[0] = "";
    		for ($i = 0; $i < count($liste); $i++)
    		{
    			$j = $liste[$i]->place_id;
    			$array[$j] = $liste[$i]->place_name;
    			//echo 'array['.$j.'] = '.$array[$j].'<br>';
    		}
    		 
    		$s = & $form->createElement('select',$label,$titre,$array,array('onchange' => $onchange, 'onclick' => $onchange));
    	
    		return $s;
    	}
    	function chargeFormByTypeVadataset($form,$label,$titre,$type,$onchange){
			$query = "select * from place where gcmd_plat_id in (select gcmd_plat_id from gcmd_plateform_keyword where gcmd_plat_name ilike '%".$type."%') AND order by place_name";
			//echo 'place.chargeFormByType: '.$query;
      		$liste = $this->getByQuery($query);
      		//$array[0] = "";
      		$x=0;
      		for ($i = 0; $i < count($liste); $i++)
        	{
          		$j = $liste[$i]->place_id;
          		$array[$j] = $liste[$i]->place_name;
          		//echo 'array['.$j.'] = '.$array[$j].'<br>';
          		if(i==0) $x=$j;
        	}
        	
        	$s = & $form->createElement('select',$label,$titre,$array,array('onchange' => $onchange , 'onclick' => $onchange , 'onload' => $onchange ));
 
      		return $s;
    	}
 		
 	}
?>

