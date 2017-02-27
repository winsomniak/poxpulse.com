<?php
        class Ability_model extends CI_Model
        {      
                function Ability_model()
                {
                        // Call the Model constructor
                        parent::__construct();
                }
                
		function ability_list()
		{
			//Grabbing ability data
			$query = mysql_query
			("
				SELECT abilities.id
				     , abilities.ability
				     , abilities.rank
				     , abilities.ap
				FROM abilities
				WHERE abilities.rank = 0 OR abilities.rank = 1  
				ORDER BY abilities.ability, abilities.rank ASC
			") or die(mysql_error());
			
			$abilities = array();
			
			while($row = mysql_fetch_array($query))
			{
				array_push($abilities, $row);
			}
			$data = $abilities;
			
			$data = $this->select_lowest_ranks($data);
			return $data;
		}
				
		function select_all()
		{
			$query =
			"
				SELECT *
				FROM abilities
			";
			
			$results = mysql_query($query);
			
			$return = array();
			
			while($row = mysql_fetch_array($results))
			{
				$return[$row['id']] = $row;
			}
			
			return $return;
		}
		
		function select_all_ids()
		{
			$query =
			"
				SELECT abilities.id
				FROM abilities
				ORDER BY abilities.id DESC
			";
			
			$results = mysql_query($query) or die(mysql_error());
			
			$return = array();
			
			while($row = mysql_fetch_array($results))
			{
				$return[$row['id']] = $row['id'];
			}
			
			return $return;
		}
		
                function select_by_id($iID)
                {
			//Selecting fields common to all runes
                        $query = mysql_query
                        ("
                                SELECT abilities.id
				     , abilities.ability
                                     , abilities.description
                                     , abilities.cooldown
                                     , abilities.rank
                                     , abilities.type
                                     , abilities.ap
                                     , abilities.icon
                                FROM abilities
                                WHERE abilities.ability LIKE (SELECT abilities.ability
                                                              FROM abilities
                                                              WHERE id = $iID)
                                ORDER BY abilities.id ASC
                        ") or die(mysql_error());
 					
                        $abilities = array();
                        
			//Reading query into an array to pass to the view file
                        while ($row = mysql_fetch_array($query))
                        {
                                array_push($abilities, $row);
                        }
                        
			if(!$abilities || $iID !== $abilities[0]['id'])
			{
				return false;
			}
                        else
                        {
                                return $abilities;
                        }
                }
                
                function select_lowest_rank_by_id($iID)
                {
			//Selecting the id of the lowest rank of the ability given
                        $query = mysql_query
                        ("
                                SELECT abilities.id
                                FROM abilities
                                WHERE abilities.ability LIKE (SELECT abilities.ability
                                                              FROM abilities
                                                              WHERE id = $iID)
                                ORDER BY abilities.id ASC
                                LIMIT 1
                        ") or die(mysql_error());
                        
			//Reading the query into an array
                        while ($row = mysql_fetch_array($query))
                        {
                                $ability = $row['id'];
                        }
                        
			if(!$ability)
			{
				return false;
			}
                        else
                        {
                                return $ability;
                        }
                }
                
                function select_lowest_ranks($array)
                {
                        //A string of IDs for the query to find the lowest rank
                        $strAbilities = '';
                        
                        //Reading the array of ids into a string for our query
                        foreach ($array as $key => $ability)
                        {
                                $strAbilities .= "'" . addslashes($ability['ability']) . "', ";
                        }
                        
			
                        //Cutting off the last comma and space
                        $strAbilities = substr($strAbilities, 0, -2);
                        
			//Selecting the id of the lowest rank of the ability given
                        $query = mysql_query
                        ("
                                SELECT MIN(abilities.id) AS id
                                     , abilities.ability
                                FROM abilities
                                WHERE abilities.ability IN ($strAbilities)
                                GROUP BY abilities.ability
                        ") or die('Ability Rank Query Error: ' . '<br />' . mysql_error());
                        
                        $abilities = array();
                        
			//Reading the query into an array
                        while ($row = mysql_fetch_array($query))
                        {
                                $ability['id'] = $row['id'];
                                $ability['ability'] = $row['ability'];
                                
                                array_push($abilities, $ability);
                        }
                        
                        foreach ($abilities as $key => $newAbility)
                        {
                                foreach ($array as $key => $originalAbility)
                                {
                                        if ($originalAbility['ability'] === $newAbility['ability'])
                                        {
                                                $array[$key]['lowest_rank'] = $newAbility['id'];
                                        }
                                }
                        }
                        
			if(!$ability)
			{
				return false;
			}
                        else
                        {
                                return $array;
                        }
                }
                
                function search_by_name($str) {
			$query = "
				SELECT abilities.ability
				     , abilities.id
				FROM abilities
				WHERE REPLACE(REPLACE(REPLACE(REPLACE(abilities.ability, ',', ''), '\'', ''), '-', ' '), ':', '') LIKE '%$str%'
				  AND (abilities.rank = 0 OR abilities.rank = 1)
                GROUP BY abilities.ability
                ORDER BY abilities.ability, MAX(abilities.id) ASC";	
			$results = mysql_query($query) or die(mysql_error());
			
			$data = array();
			
			while ($row = mysql_fetch_array($results))
			{
				array_push($data, $row);
			}
			
			return $data;
		}
		
		//Selects and returns all flavor text for runes, with the rune ID as the key
		function select_icons()
		{
			$query =
			"
				SELECT id, icon
				FROM abilities
				ORDER BY id DESC
			";
			
			$results = mysql_query($query) or die(mysql_error());
			
			$data = array();
			while($row = mysql_fetch_array($results))
			{
				$data[$row['id']] = $row['icon'];
			}
			
			return $data;
		}
                
        }
?>