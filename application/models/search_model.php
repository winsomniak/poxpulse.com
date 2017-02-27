<?php
        class Search_model extends CI_Model
        {      
                function Search_model()
                {
                        // Call the Model constructor
                        parent::__construct();
                        $this->load->model('Ability_model');
                }
                
                function advanced_search($str)
                {
                        //$parameters = parse_search_url($str);
						$parameters = $this->uri->ruri_to_assoc(3);
			
                        //Creating strings to hold the different parts of our query
                        $strWhere = 'types.id = runes.type_id ';
                        $strTables = 'runes, types, ';
                        
                        //Creating an array to hold the factions of a given rune
                        $factions = array();
                        
                        //Rune
                        if (isset($parameters['rune']))
                        {
                                $strWhere .= " AND runes.rune LIKE '%" . $parameters['rune'] . "%'";
                        }
                        
                        //Type
                        if (isset($parameters['type']))
                        {
                                $strWhere .= " AND runes.type_id = " . $parameters['type'];
                        }
                        
                        //Rarity
                        if (isset($parameters['rarity']))
                        {
                                $strWhere .= " AND runes.rarity_id = " . $parameters['rarity'];
                        }
                        
                        //Race
                        if (isset($parameters['race']))
                        {
                                $strWhere .= " AND champion_races.race_id = " . $parameters['race'];
                        }
                        
                        //Class
                        if (isset($parameters['class']))
                        {
                                $strWhere .= " AND champion_classes.class_id = " . $parameters['class'];
                        }
                        
			//Nora Cost
			if (isset($parameters['noramin']))
			{
				$strWhere .= " AND runes.nora_cost >= " . $parameters['noramin'];
			}
			
			if (isset($parameters['noramax']))
			{
				$strWhere .= " AND runes.nora_cost <= " . $parameters['noramax'];
			}
			
			//Expansion
			if (isset($parameters['expansion']))
			{
				$strWhere .= " AND runes.expansion_id = " . $parameters['expansion'];
			}
			
			//Ability
			
                        //Faction 1
                        if (isset($parameters['faction1']))
                        {
							
                                array_push($factions, $parameters['faction1']);  
                        }
                        
                        //Faction 2
                        if (isset($parameters['faction2']))
                        {
                                array_push($factions, $parameters['faction2']);  
                        }
			
                        
                        if (count($factions) > 0)
                        {
                                $strTables .= 'rune_factions, ';
                                $strWhere .= ' AND runes.id IN (';
                        }
                        
                        if (count($factions) == 1)
                        {
                                $factionID = $factions[0];
                                $strWhere .= "SELECT rune_factions.rune_id FROM rune_factions WHERE rune_factions.faction_id = $factionID)";
                        }
                        
                        if (count($factions) == 2)
                        {
                                $strWhere .= "SELECT rune_factions.rune_id FROM rune_factions WHERE rune_factions.faction_id = " . $factions[0];
								
								if(isset($parameters['flogic']) && $parameters['flogic'] == 'and')
								{
									$strWhere .= " AND runes.id IN (SELECT rune_factions.rune_id FROM rune_factions WHERE rune_factions.faction_id = " . $factions[1] . "))";
								}
								else
								{
									$strWhere .= " OR rune_factions.faction_id = " . $factions[1] . ")";
								}
                                
                        }
                        
                        $strTables = substr($strTables, 0, -2);
                        
						//Query could probably be more efficient. GROUP BY is a work-around for the issue of runes being duplicated if they have two factions.
                        $query =
                        "
                                SELECT runes.rune
									 , types.type
									 , rarities.rarity
									 , runes.id AS id
									 , expansions.expansion
									 , f1.faction AS 'faction_one'
									 , f2.faction AS 'faction_two'
								FROM runes
								LEFT JOIN rarities ON rarities.id = runes.rarity_id
								LEFT JOIN types ON types.id = runes.type_id
								LEFT JOIN expansions ON expansions.id = runes.expansion_id
								LEFT JOIN rune_factions rf1 ON rf1.rune_id = runes.id
								LEFT OUTER JOIN factions f1 ON f1.id = rf1.faction_id
								LEFT JOIN rune_factions rf2 ON rf2.rune_id = runes.id
								  AND rf1.faction_id != rf2.faction_id
								LEFT OUTER JOIN factions f2 ON f2.id = rf2.faction_id
								LEFT JOIN champion_classes ON champion_classes.rune_id = runes.id
								LEFT JOIN champion_races ON champion_races.rune_id = runes.id
								WHERE $strWhere
								GROUP BY runes.id ASC
								ORDER BY runes.rune
												
						";
			
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $runes = array();
                        
                        while($row = mysql_fetch_array($results))
                        {
                               array_push($runes, $row);
                        }
                        
                        return $runes;
                                
                }
                function search_factions()
                {
                        $query = "
                                SELECT factions.faction, factions.id
                                FROM factions
                                ORDER BY factions.faction ASC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
		
		function search_faction_by_name($str)
		{
			$query =
			"
				SELECT * FROM factions
				WHERE REPLACE(factions.faction, '\'', '') = '$str'
			";
			$results = mysql_query($query) or die(mysql_error());
			
			$faction = mysql_fetch_array($results);
			
			return $faction;
		}
                
                function search_expansions()
                {
                        $query = "
                                SELECT expansions.expansion, expansions.id
                                FROM expansions
                                ORDER BY expansions.id DESC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                 $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
                
                function search_races()
                {
                        $query = "
                                SELECT races.race, races.id
                                FROM races
                                ORDER BY races.race ASC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
                
                function search_classes()
                {
                        $query = "
                                SELECT classes.class, classes.id
                                FROM classes
                                ORDER BY classes.class ASC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                               $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
                
                function search_types()
                {
                        $query = "
                                SELECT types.type, types.id
                                FROM types
                                ORDER BY types.type ASC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
                
                function search_rarities()
                {
                        $query = "
                                SELECT rarities.rarity, rarities.id
                                FROM rarities
                                ORDER BY rarities.id ASC
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                $data[$row['id']] = $row;
                        }
                        
                        return $data;
                }
                
                function search_runes_by_faction($str)
                {
                        $query = "
                                SELECT runes.rune
                                     , runes.id
                                     , types.type
                                FROM runes, factions, rune_factions, types
                                WHERE REPLACE(factions.faction, '\'', '') = '$str'
                                  AND factions.id = rune_factions.faction_id
                                  AND runes.id = rune_factions.rune_id
                                  AND types.id = runes.type_id
                                ORDER BY runes.rune
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                array_push($data, $row);
                        }
                        
                        return $data;
                }
                
                function search_runes_by_expansion($str)
                {
                        $query = "
                                SELECT runes.rune
                                     , runes.id
                                     , types.type
                                FROM runes, expansions, types
                                WHERE REPLACE(expansions.expansion, '\'', '') = '$str'
                                  AND runes.expansion_id = expansions.id
                                  AND types.id = runes.type_id
                                ORDER BY runes.rune
                        ";
                        
                        $results = mysql_query($query) or die(mysql_error());
                        
                        $data = array();
                        
                        while ($row = mysql_fetch_array($results))
                        {
                                array_push($data, $row);
                        }
                        
                        return $data;
                }
                
                
        }
?>