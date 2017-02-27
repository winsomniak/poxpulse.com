<?php
        class Rune_model extends CI_Model
        {      
                function Rune_model()
                {
                        // Call the Model constructor
                        parent::__construct();
                        $this->load->model('Ability_model');
                }
                
                function select_by_id($iID)
                {
			//Selecting fields common to all runes
                        $rune_query = mysql_query
                        ("
                                SELECT runes.id
				     , runes.rune
				     , runes.quote
				     , runes.nora_cost
				     , types.type
				     , rarities.rarity
				     , artists.first_name as artist_first_name
				     , artists.last_name as artist_last_name
				     , expansions.expansion
                                FROM runes
				   , types
				   , rarities
				   , artists
				   , expansions
                                WHERE runes.id = $iID
				  AND runes.type_id = types.id
				  AND runes.rarity_id = rarities.id
				  AND runes.artist_id = artists.id
				  AND runes.expansion_id = expansions.id
                        ") or die(mysql_error());
 						
			//Reading the first row (and only row) into an array
                        $data = mysql_fetch_array($rune_query);
			if(!$data)
			{
				return false;
			}
			//If the rune is a champion rune, we select extra fields specific to champions		
			if ($data['type'] == 'champion')
			{                                
				$champion_query = mysql_query
				("
					SELECT champions.hp
					     , champions.speed
					     , champions.defense
					     , champions.damage
					     , champions.min_range
					     , champions.max_range
					     , champions.size
					FROM champions
					WHERE champions.rune_id = " . $data['id']
				) or die(mysql_error());
				//Adding the champion data to the data previously queried data
				$data = array_merge($data, mysql_fetch_array($champion_query));
				
				//Grabbing ability data
				$query = mysql_query
				("
					SELECT abilities.id
					     , abilities.ability
					     , abilities.description
					     , abilities.cooldown
					     , abilities.rank
					     , abilities.type
					     , abilities.ap
					FROM abilities
					   LEFT JOIN champion_base_abilities
					   ON champion_base_abilities.ability_id = abilities.id
                                           
					   
					WHERE champion_base_abilities.rune_id = $iID
                                        ORDER BY abilities.ability ASC
				") or die(mysql_error());
				
				$baseAbilities = array();
				
				while($row = mysql_fetch_array($query))
				{
					array_push($baseAbilities, $row);
				}
				$data['baseAbilities'] = $baseAbilities;
                                
                                $data['baseAbilities'] = $this->Ability_model->select_lowest_ranks($data['baseAbilities']);
				
				//Selecting upgrade abilities from the database
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
					   LEFT JOIN champion_upgrade_abilities
					   ON champion_upgrade_abilities.ability_id = abilities.id
					   
					WHERE champion_upgrade_abilities.rune_id = $iID
                                        ORDER BY champion_upgrade_abilities.upgrade_slot ASC
					  
				") or die(mysql_error());
				
				$upgradeAbilities = array();
				
				while($row = mysql_fetch_array($query))
				{
					array_push($upgradeAbilities, $row);
				}
				$data['upgradeAbilities'] = $upgradeAbilities;
                                
                                $data['upgradeAbilities'] = $this->Ability_model->select_lowest_ranks($data['upgradeAbilities']);
                                
                                //Selecting classes from the database
                                $query = mysql_query
                                ("
                                        SELECT classes.class
                                             , classes.id
                                        FROM classes
                                        LEFT JOIN champion_classes
                                        ON champion_classes.class_id = classes.id
                                        WHERE champion_classes.rune_id = $iID
                                ");
                                
                                $classes = array();
                                
                                if ($query)
                                {
                                        while ($row = mysql_fetch_array($query))
                                        {
                                                array_push($classes, $row);
                                        }
                                        $data['classes'] = $classes;
                                }
                                
                                //Selecting races from the database
                                $query = mysql_query
                                ("
                                        SELECT races.race
                                             , races.id
                                        FROM races
                                        LEFT JOIN champion_races
                                        ON champion_races.race_id = races.id
                                        WHERE champion_races.rune_id = $iID
                                ");
                                
                                $races = array();
                                
                                if ($query)
                                {
                                        while ($row = mysql_fetch_array($query))
                                        {
                                                array_push($races, $row);
                                        }
                                        $data['races'] = $races;
                                }
                                
			}
			//Otherwise we don't have to, but we DO need a description, which champion runes do not have.
			else
			{
				$description_query = mysql_query
				("
					SELECT rune_descriptions.description
					FROM rune_descriptions
					WHERE rune_descriptions.rune_id = $iID
				") or die(mysql_error());
				//Adding the description to the data previously queried
				$data = array_merge($data, mysql_fetch_array($description_query));
			}
			
			//Querying for the faction(s) that the rune belongs to.
			$faction_query = mysql_query
			("
				SELECT factions.faction
				     , factions.abbreviation
				FROM factions, rune_factions
				WHERE rune_factions.rune_id = $iID
				  AND rune_factions.faction_id = factions.id
                                ORDER BY factions.abbreviation
			") or die(mysql_error());
			
			//We use a counter simply to ensure that the faction and abbreviation stay together
			$counter = 0;
			//Reading the factions into an array
			while($row = mysql_fetch_array($faction_query))
			{
				$factions[$counter]['faction'] = $row['faction'];
				$factions[$counter]['abbreviation'] = $row['abbreviation'];
				$counter++;
			}
			//Adding the factions array to our data
			$data['factions'] = $factions;
			
			/*
			echo '<pre>';
			print_r($data);
			echo '</pre>';
			*/
			
			//Returning all of our work
			return $data;
                }
		
		function search_by_name($str) {
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
				WHERE REPLACE(REPLACE(REPLACE(runes.rune, ',', ''), '\'', ''), '-', ' ') LIKE '%$str%'
				GROUP BY runes.id


			";
				
			$results = mysql_query($query) or die(mysql_error());
			
			$data = array();
			
			while ($row = mysql_fetch_array($results))
			{
				array_push($data, $row);
			}
			
			return $data;
		}
		
		//Inserts appropriate anchor tags within the descriptions field
                function create_description_link($word, $url)
		{
			$replacement = '<a href="' . $url . '">' . $word . '</a>';
			
			$query =
			"
				UPDATE rune_descriptions
				SET rune_descriptions.description = REPLACE(rune_descriptions.description, '$word', '$replacement')
				WHERE rune_descriptions.description NOT LIKE '%$replacement%';
			";
			
			$results = mysql_query($query) or die(mysql_error());
			
		}
		
                function search_by_ability($id)
                {
                        $query =
                        "
                        SELECT abilities.id
                        FROM abilities
                        WHERE abilities.ability = (SELECT abilities.ability
                                                   FROM abilities
                                                   WHERE abilities.id = $id)
                        ";
                        
                        $ids = mysql_query($query) or die(mysql_error());
                        
                        $str = '';
                        while ($row = mysql_fetch_array($ids))
                        {
                                $str .= $row['id'] . ', ';
                        }
                        $str = substr($str, 0, -2);
                        
                       $query =
                       "
				(SELECT champion_base_abilities.rune_id as id
				     , abilities.rank
                                     , runes.rune as 'rune'
				     , f1.faction AS 'faction_one'
				     , f2.faction AS 'faction_two'
                                FROM champion_base_abilities, abilities, runes
				LEFT JOIN rune_factions rf1 ON rf1.rune_id = runes.id
				LEFT OUTER JOIN factions f1 ON f1.id = rf1.faction_id
				LEFT JOIN rune_factions rf2 ON rf2.rune_id = runes.id
				  AND rf1.faction_id != rf2.faction_id
				LEFT OUTER JOIN factions f2 ON f2.id = rf2.faction_id
                                WHERE champion_base_abilities.ability_id IN ($str)
                                  AND runes.id = champion_base_abilities.rune_id
				  AND abilities.id = champion_base_abilities.ability_id)
                                
                                UNION
                                
                                (SELECT champion_upgrade_abilities.rune_id
				     , abilities.rank
                                     , runes.rune
				     , f1.faction AS 'faction_one'
				     , f2.faction AS 'faction_two'
                                FROM champion_upgrade_abilities, abilities, runes
				LEFT JOIN rune_factions rf1 ON rf1.rune_id = runes.id
				LEFT OUTER JOIN factions f1 ON f1.id = rf1.faction_id
				LEFT JOIN rune_factions rf2 ON rf2.rune_id = runes.id
				  AND rf1.faction_id != rf2.faction_id
				LEFT OUTER JOIN factions f2 ON f2.id = rf2.faction_id
                                WHERE champion_upgrade_abilities.ability_id IN ($str)
                                  AND runes.id = champion_upgrade_abilities.rune_id
				  AND abilities.id = champion_upgrade_abilities.ability_id)
                                ORDER BY rune, rank
                        ";
                                
				
			$results = mysql_query($query) or die(mysql_error());
			
			$champions = array();
			
			while ($row = mysql_fetch_array($results))
			{
				array_push($champions, $row);
			}
			
                        
			return $champions; 
                }
                
		//Selects and returns all flavor text for runes, with the rune ID as the key
		function select_quotes()
		{
			$query =
			"
				SELECT id, quote
				FROM runes
				ORDER BY id DESC
			";
			
			$results = mysql_query($query) or die(mysql_error());
			
			$data = array();
			while($row = mysql_fetch_array($results))
			{
				$data[$row['id']] = $row['quote'];
			}
			
			return $data;
		}
		
		
		//Selects and returns all faction IDs associated with rune IDs
		function select_rune_factions()
		{
			$query =
			"
				SELECT faction_id, rune_id
				FROM rune_factions
				ORDER BY rune_id ASC
			";
			
			$results = mysql_query($query) or die(mysql_error());
			
			$pairs = array();
			$runes = array();
			
			//Moving the key pairs into an array
			while($row = mysql_fetch_array($results))
			{
				array_push($pairs, array('rune_id' => $row['rune_id'], 'faction_id' => $row['faction_id']));
			}
			
			echo count($pairs);
			//loop to create an array for each rune
			foreach($pairs as $key => $pair)
			{
				if(!array_key_exists($pair['rune_id'], $runes))
				{
					echo 'New key... ' . $pair['rune_id'] . '<br />';
					echo '<pre>' . print_r($pair) . '</pre>';
					$runes[$pair['rune_id']] = array($pair['faction_id']);
				}
				else
				{
					echo 'Existing key...<br />';
					array_push($runes[$pair['rune_id']], $pair['faction_id']);
				}
			}
			
			echo '<pre>';
			print_r($runes);
			echo '</pre>';
			return $runes;
		}
		
		function select_random_rune()
		{
			$query =
			"
				SELECT id
				FROM runes
				ORDER BY RAND()
				LIMIT 1
			";
			
			$result = mysql_query($query) or die(mysql_error());
			
			$rune = mysql_fetch_array($result);
			
			return $rune['id'];
		}
                
        }
?>