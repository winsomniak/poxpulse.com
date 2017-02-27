<?php

class Xml_feed_model extends CI_Model
{      
	function Xml_feed_model()
	{
		// Call the Model constructor
		parent::__construct();
		
		$this->load->model('Ability_model');
		$this->load->model('Rune_model');
	}
	
	//Scans the XML for all NEW stuff
	function scan($existingData)
	{
		//Retrieve xml file and parse into an array
		$data = $this->xml2array(file_get_contents(site_url() . '/xmlfeed.xml'));
		$data['existingRuneFactions'] = $existingData['existingRuneFactions'];
		//Scan xml for NEW content
		echo '<p>Extracting data...';
		
		echo '<p>Scanning for new artists...';
		$this->extract_artists($data, false);
		
		echo '<p>Scanning for new classes...';
		$this->extract_classes($data, false);
		
		echo '<p>Scanning for new races...';
		$this->extract_races($data, false);
		
		echo '<p>Scanning for new abilities...';
		$this->extract_abilities($data, false);
		
		echo '<p>Scanning for new champions...';
		$this->extract_champions($data, false);
		
		echo '<p>Scanning for new runes that are not champions...';
		$this->extract_runes($data, false);
		
		//Scan xml for changes to existing content
		//echo '<p>Scanning for changes to existing abilities...';
		//$this->update_abilities($data, false);
	}
	
	
	/* freshScan() 
	 *
	 * freshScan() is designed to drop all previously stored data from the XML, and insert 
	 * it from a fresh XML scan. freshScan() will not drop anything that isn't from the XML 
	 * such as icons for abilities, and flavor text for runes
	 *
	 * It is important to note that freshScan() updating properly depends upon the XML ids 
	 * remaining the same as they were prior to the scan. For instance, swarm: carrionling, if ID 17 
	 * needs to remain 17 after the scan.
	*/
	function freshScan()
	{
		//Move all data NOT contained in the xml to temporary arrays
		$quotes = $this->Rune_model->select_quotes();
		$icons = $this->Ability_model->select_icons();
		$existingRuneFactions = $this->Rune_model->select_rune_factions();
		
		//Emptying out the data in the database related to the xml
		echo "Dropping all existing XML data";
		$this->emptyDatabase();
		$data['existingRuneFactions'] = $existingRuneFactions;
		//Adding any data related to the xml into the database
		$this->scan($data);
		
		//Rejoin all quotes and icons to the data
		foreach($quotes as $id => $quote)
		{
			$quote = mysql_real_escape_string($quote);
			$query = 
			"
				UPDATE runes
				SET runes.quote = '$quote'
				WHERE runes.id = $id
			";
			mysql_query($query) or die($query. '<br />' . mysql_error());
		}
		
		foreach($icons as $id => $icon)
		{
			$query = 
			"
				UPDATE abilities
				SET icon = $icon
				WHERE id = $id
			";
			mysql_query($query) or die(mysql_error());
		}
		
	}
	
	/* emptyDatabase()
	 *
	 * Empties all tables associated with the XML in the database
	 *
	*/
	
	function backupDatabase()
	{
		
		$mysqldump_location = "";
		$db_host = "localhost";
		$db_user = "root";
		$db_pass = "";
		$db_name = "poxpulse";
		$filename = "backups/database/poxpulse_" . date("m.d.y") . ".sql";
		
		$command = $mysqldump_location . "mysqldump --host=".$db_host." --user=".$db_user." --password=".$db_pass." --result-file=".$filename. " " .$db_name; 
		echo "running command: " . $command;
		system($command);
	}
	
	function loadDatabaseBackup($file)
	{
		mysql_query("SOURCE " . $file) or die(mysql_error());
	}
	
	function emptyDatabase()
	{
		//Tables to be truncated
		$tables = array
		(
			'abilities',
			'artists',
			'champions',
			'champion_base_abilities',
			'champion_upgrade_abilities',
			'champion_classes',
			'champion_races',
			'classes',
			'races',
			'runes',
			'rune_descriptions',
			'rune_factions'
		);
		
		foreach($tables as $key => $table)
		{
			mysql_query("TRUNCATE $table");
		}
				
	}
	/* extract_artists($data)
	*
	* This function expects xml2array(file_get_contents('xmlfeed.xml')) as a parameter.
	* xmlfeed.xml should be the downloaded poxnora feed
	* This is essentially throw away code, it will probably not work with any other
	* feed and will not work if they change their xml structure. I am hoping that
	* it will remain useful, however.
	*
	* extract_artists does not return any values, it simply prints some messages to
	* the screen and inserts into poxpulse's database.
	*/
       function extract_artists($data, $test = true)
       {
	       //Selecting all artists already present in the database
	       $query = "SELECT artists.first_name, artists.last_name FROM artists";
	       $results = mysql_query($query) or die(mysql_error());
	       
	       //Creating an array to store the database results
	       $existingArtists = array();
	       
	       //Reading the results into an array
	       while($row = mysql_fetch_array($results))
	       {
		       array_push($existingArtists, $row['first_name'] . ' ' . $row['last_name']);
	       }
	       
	       //Simplifying our super-dimensional array, slightly
	       $runes['champions'] = $data['runes']['champions'];
	       $runes['spells'] = $data['runes']['spells'];
	       $runes['equipment'] = $data['runes']['equipment'];
	       $runes['relics'] = $data['runes']['relics'];
	       
	       //Storing the singular form for loop purposes.
	       $runes['champions']['single'] = 'champ';
	       $runes['spells']['single'] = 'spell';
	       $runes['equipment']['single'] = 'equip';
	       $runes['relics']['single'] = 'relic';
	       
	       //Creating arrays to hold our artists
	       $artists = array();
	       $fullNames = array();
	       
	       foreach($runes as $key => $category)
	       {
		       foreach ($category[$category['single']] as $key => $rune)
		       {
				$rune['artist'] = str_replace('"', '', $rune['artist']);
			       if (!in_array($rune['artist'], $fullNames) && !in_array(strtolower($rune['artist']), $existingArtists))
			       {
					   
				       $fullNames[] = $rune['artist'];
					   $numSpaces = substr_count(trim($rune['artist']), ' ');
					   if($numSpaces > 0)
					   {
						   $space = trim(strpos($rune['artist'], ' '));
						   $firstName = trim(strtolower(substr($rune['artist'], 0, $space)));
						   $lastName = trim(strtolower(substr($rune['artist'], $space, strlen($rune['artist']))));
					   }
					   else
					   {
					       $firstName = trim(strtolower($rune['artist']));
						   $lastName = "";
					   }
				       $arrName = array('firstName' => $firstName, 'lastName' => $lastName);
				       array_push($artists, $arrName);
			       }
		       }
	       }
	       
	       //Creating and initializing our query value string
	       $values = '';
	       
	       //Concatenating each new artist to the values string to be inserted
	       foreach ($artists as $key => $name)
	       {
		       $values .= "('" . $name['firstName'] . "', '" . $name['lastName'] . "'), ";
	       }
	       
	       //Removing the last ', ' from the values
	       $values = substr($values, 0, -2);
	       
	       //Storing the query string in a variable
	       $query = "
		       INSERT INTO artists(first_name, last_name)
		       VALUES $values";
	       
	       //Printing the query to the screen so we can see what is going on
	       if ($values == '')
	       {
		       echo '<p>There were no new artists to be inserted into the database.</p>';
	       }
	       else
	       {
			echo '<pre>';
			print_r($artists);
			print_r($fullNames);
			print_r($existingArtists);
			echo '</pre>';
		       echo '<p>Artist Insertion Query:<br />' . $query . '</p>';
		       
		       //Executing our query and inserting all of the new artists, if it is not a test
		       if (!$test)
		       {
			       mysql_query($query) or die(mysql_error());
		       }
	       }   
       }
       
       
       /* extract_classes($data)
	*
	* This function expects xml2array(file_get_contents('xmlfeed.xml')) as a parameter.
	* xmlfeed.xml should be the downloaded poxnora feed
	* This is essentially throw away code, it will probably not work with any other
	* feed and will not work if they change their xml structure. I am hoping that
	* it will remain useful, however.
	*
	* extract_classes does not return any values, it simply prints some messages to
	* the screen and inserts into poxpulse's database.
	*/
       
       function extract_classes($data, $test = true)
       {
	       //Selecting all classes already present in the database
	       $query = "SELECT classes.class FROM classes";
	       $results = mysql_query($query) or die(mysql_error());
	       
	       //Creating an array to store the database results
	       $existingClasses = array();
	       
	       //Reading the results into an array
	       while($row = mysql_fetch_array($results))
	       {
		       array_push($existingClasses, $row['class']);
	       }
	       
	       //Simplifying our super-dimensional array a bit
	       $champions = $data['runes']['champions'];
	       //Creating an array to store the new classes
	       $classes = array();
	       
	       //Retrieving the new classes
	       foreach ($champions['champ'] as $key => $champ)
	       {
		       //Trimming off any extra white space and converting the class to lowercase
		       $strClasses = trim(strtolower($champ['classes']));
		       
		       //If the class is stored as 'none', we skip it. Pointless storage.
		       if ($strClasses == 'none')
		       {
			       continue;
		       }
		       
		       //Counting the number of spaces in the class string, aka how many more than 1 class there is.
		       $spaceCount = substr_count($strClasses, ' ');
		       
		       //If there is at least one space, we know there is more than one class
		       if ($spaceCount > 0)
		       {
			       //Finding the location of the first space character
			       $firstSpace = strpos($strClasses, ' ');
			       
			       //Initializing the first space string position
			       $spacePos = $firstSpace;
			       
			       //Setting the remaining string to parse to the entire thing, we just started!
			       $strRemaining = $strClasses;
			       
			       //For every space we counted, we insert a class
			       for ($i = 0; $i <= $spaceCount; $i++)
			       {
				       //Checking if we are on the last space, and if so, pretend there is a space at the end of the string
				       if ($spacePos == '')
				       {
					       $spacePos = strlen($strRemaining);
				       }
				       
				       //Grabbing the class, and ensuring a second time that there are no extra space characters
				       $class = trim(substr($strRemaining, 0, $spacePos));
				       
				       //Checking that the class is not already in the list of classes to insert and does not exist in the database, before proceeding.
				       if (!in_array($class, $classes) && !in_array($class, $existingClasses))
				       {
					       array_push($classes, $class);
				       }
				       
				       //Cutting off the class from the string
				       $strRemaining = substr($strRemaining, $spacePos + 1);
				       
				       //Finding the position of the next space character
				       $spacePos = strpos($strRemaining, ' ');
			       }
		       }
		       //If there is only one class, we simply check if its in the insertion list or the database before adding it.
		       else
		       {
			       $class = $strClasses;
			       if (!in_array($class, $classes) && !in_array($class, $existingClasses))
			       {
				       array_push($classes, $class);
			       }
		       }
	       }
	       
	       //Initializing our insertion string
	       $values = '';
	       
	       //Adding each class to the insertion string
	       foreach ($classes as $key => $class)
	       {
		       $values .= "('" . addslashes($class) . "'),";
	       }
	       
	       //cutting off the last ',' from the insertion string
	       $values = substr($values, 0, -1);
	       
	       //Saving our insertion query in a variable
	       $query = "
		       INSERT INTO classes(class)
		       VALUES $values";
	       
	       //Printing the query to the screen so we can see what is going on
	       if ($values == '')
	       {
		       echo '<p>There were no new classes to be inserted into the database.</p>';
	       }
	       else
	       {
		       echo '<p>Class Insertion Query:<br />' . $query . '</p>';
		       
		       //Executing our query and inserting all of the new classes, if it is not a test
		       if (!$test)
		       {
			       mysql_query($query) or die(mysql_error());
		       }
	       }   
       }
       
       
       
       /* extract_races($data)
	*
	* This function expects xml2array(file_get_contents('xmlfeed.xml')) as a parameter.
	* xmlfeed.xml should be the downloaded poxnora feed
	* This is essentially throw away code, it will probably not work with any other
	* feed and will not work if they change their xml structure. I am hoping that
	* it will remain useful, however.
	*
	* extract_races does not return any values, it simply prints some messages to
	* the screen and inserts into poxpulse's database.
	*/
       
       function extract_races($data, $test = true)
       {
	       //Selecting all races already present in the database
	       $query = "SELECT races.race FROM races";
	       $results = mysql_query($query) or die(mysql_error());
	       
	       //Creating an array to store the database results
	       $existingRaces = array();
	       
	       //Reading the results into an array
	       while($row = mysql_fetch_array($results))
	       {
		       array_push($existingRaces, $row['race']);
	       }
	       
	       //Simplifying our super-dimensional array a bit
	       $champions = $data['runes']['champions'];
	       
	       //Creating an array to store the classes to be inserted
	       $races = array();
	       
	       //Iterating through every champion in the xml and scanning for new races        
	       foreach ($champions['champ'] as $key => $champ)
	       {
		       //Trimming off any extra whitespace and converting to lowercase
		       $strRaces = trim(strtolower($champ['races']));
		       
		       //Counting the number of commas in the races string, and thus how many races
		       $commaCount = substr_count($strRaces, ',');
		       
		       //If there is at least one comma, we know there is more than one race
		       if ($commaCount > 0)
		       {
			       //Locating the first comma
			       $firstComma = strpos($strRaces, ',');
			       
			       //Initializing comma points
			       $commaPos = $firstComma;
			       
			       //Setting the remaining string to scan, to the entire thing. We just started!
			       $strRemaining = $strRaces;
			       
			       //Checking every race in the string
			       for ($i = 0; $i <= $commaCount; $i++)
			       {
				       //If the comma position is unset, we know we are at the end
				       if ($commaPos == '')
				       {
					       $commaPos = strlen($strRemaining);
				       }
				       
				       //Grabbing the race from the string and double checking for any extra whitespace
				       $race = trim(substr($strRemaining, 0, $commaPos));
				       
				       //Checking that the race is not in the insertion array or in the database before proceeding
				       if (!in_array($race, $races) && !in_array($race, $existingRaces))
				       {
					       array_push($races, $race);
				       }
				       
				       //Chopping off the race from the string
				       $strRemaining = substr($strRemaining, $commaPos + 1);
				       
				       //Locating the next comma
				       $commaPos = strpos($strRemaining, ',');
			       }
		       }
		       //If there are no commas, we know there is only one race
		       else
		       {
			       $race = $strRaces;
			       
			       //Checking that the race is not in the insertion array or in the database before proceeding
			       if (!in_array($race, $races) && !in_array($race, $existingRaces))
			       {
				       array_push($races, $race);
			       }
		       }
	       }
	       
	       //Initializing the insertion string
	       $values = '';
	       
	       //Concatenating each new race to the insertion string
	       foreach ($races as $key => $race)
	       {
		       $values .= "('" . addslashes($race) . "'),";
	       }
	       
	       //Chopping off the last ',' from the insertion string
	       $values = substr($values, 0, -1);
	       
	       //Storing the query string in a variable
	       $query = "
		       INSERT INTO races(race)
		       VALUES $values";
	       
	       //Printing the query to the screen so we can see what is going on
	       if ($values == '')
	       {
		       echo '<p>There were no new races to be inserted into the database.</p>';
	       }
	       else
	       {
		       echo '<p>Race Insertion Query:<br />' . $query . '</p>';
		       
		       //Executing our query and inserting all of the new races, if it is not a test
		       if (!$test)
		       {
			       mysql_query($query) or die(mysql_error());
		       };
	       }   
       }
       
       
       /* extract_abilities($data)
	*
	* This function expects xml2array(file_get_contents('xmlfeed.xml')) as a parameter.
	* xmlfeed.xml should be the downloaded poxnora feed
	* This is essentially throw away code, it will probably not work with any other
	* feed and will not work if they change their xml structure. I am hoping that
	* it will remain useful, however.
	*
	* extract_abilities does not return any values, it simply prints some messages to
	* the screen and inserts into poxpulse's database.
	*/
       
       function extract_abilities($data, $test = true)
       {
		//Retrieving the IDs of all existing abilities
		$existingAbilities = $this->Ability_model->select_all_ids();
	       
		//Creating an array to model the expected structure of the ability arrays
		$abilityModel = array
		(
			'id' => 0,
			'ap' => 0,
			'name' => 0,
			'desc' => 0,
			'type' => 0,
			'rank' => 0,
			'cooldown' => 0
		);
			     
		//Simplifying our super-dimensional array a bit
		$champions = $data['runes']['champions'];
		
		//Creating an array to store all of the new abilities to be inserted
		$newAbilities = array();
		
		//Create an array to store the IDs of the new abilities, so we don't insert them multiple times
		$queuedAbilities = array();
		
		//Creating an array to keep track of anomalies/abilities that do not follow array structure
		$anomalies = array();
		
		//Creating an array to 
		$championAbilities = array();
		
		//Combining starting_abilities and upgrade abilities into a single array
		foreach ($champions['champ'] as $key => $champ)
		{

			
			//Checking the base/starting abilities
			foreach ($champ['starting_abilities']['ability'] as $key => $ability)
			{
				array_push($championAbilities, $ability);
			}
		       
			//Checking the upgrade abilities
			foreach ($champ['upgrade_abilities']['ability'] as $key => $ability)
			{
				
				array_push($championAbilities, $ability);
			}
		}
		
		//Checking every ability for "new" status, and whether or not it is already flagged for insertion
		foreach($championAbilities as $key => $ability)
		{
			//Checking that the ability conforms to the expected array structure, to preven weird anomalies
				if(!array_structure_matches($ability, $abilityModel))
				{
					array_push($anomalies, $ability);
					continue;
				}
				
				//Checking that the ability is not in the insertion array or the database already before proceeding
				if ( (!in_array($ability['id'], $queuedAbilities)) && (!in_array($ability['id'], $existingAbilities)))
				{
					//Adding the ability to the queue of abilities to be inserted
					array_push($queuedAbilities, $ability['id']);
					$newAbilities[$ability['id']] = $ability;
				}
		}
		
		//Initializing our insertion string
		$values = '';
		
		//Adding all new abilities to the insertion string
		foreach ($newAbilities as $key => $ability)
		{
			//Removing extra white space, converting to lowercase, and escaping characters for the ability name
			$ability['name'] = trim(strtolower(addslashes($ability['name'])));
			
			//Escaping characters and removing any extra white space for the ability description
			$ability['desc'] = trim(addslashes($ability['desc']));
			
			//Concatenating the entire set of ability data to the insertion string
			$values .= "(" . $ability['id'] . ", '" . $ability['name'] . "', '" . $ability['desc'] . "', " . $ability['cooldown'] . ", " . $ability['rank'] . ", " . $ability['type'] . ", " . $ability['ap'] . "), ";
		}
		
		//Cutting off the last ', ' from the insertion string
		$values = substr($values, 0, -2);
		
		//Storing the query string in a variable
		$query = "
			INSERT INTO abilities(id, ability, description, cooldown, rank, type, ap)
			VALUES $values";
		
		
		//Printing the query to the screen so we can see what is going on
		if ($values == '')
		{
			echo '<p>There were no new abilities to be inserted into the database.</p>';
		}
		else
		{
			echo '<p>Ability Insertion Query:<br />' . $query . '</p>';
			
			//Executing our query and inserting all of the new abilities, if it is not a test
			if (!$test)
			{
				mysql_query($query) or die(mysql_error());
			}
		} 
	}
       
       
       
       
       /* extract_champions($data)
	*
	* This function expects xml2array(file_get_contents('xmlfeed.xml')) as a parameter.
	* xmlfeed.xml should be the downloaded poxnora feed
	* This is essentially throw away code, it will probably not work with any other
	* feed and will not work if they change their xml structure. I am hoping that
	* it will remain useful, however.
	*
	* extract_champions does not return any values, it simply prints some messages to
	* the screen and inserts into poxpulse's database.
	*/
       
       function extract_champions($data, $test = true)
       {
	       $existingRuneFactions = $data['existingRuneFactions'];
	       $xmlRuneFactions = array();
	       
	       //Selecting rarity IDs and values from the database to associate with runes
	       $query = "SELECT * FROM rarities";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       $rarities[$row['rarity']] = $row['id'];
	       }
	       
	       
	       //Selecting faction IDs and values from the database to associate with runes
	       $query = "SELECT * FROM factions";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       $factions[$row['faction']] = $row['id'];
	       }
	       
	       
	       //Selecting expansion IDs and values from the database to asscoaite with runes
	       $query = "SELECT * FROM expansions";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       //exceptions, because I have made changes.
		       switch ($row['expansion'])
		       {
			       case 'poxnora release':
				       $row['expansion'] = 'poxnora release set';
				       break;
			       case 'savage tundra':
				       $row['expansion'] = 'savage tundra expansion';
				       break;
			       case 'shattered peaks':
				       $row['expansion'] = 'shattered peaks expansion';
				       break;
			       case 'drums of war':
				       $row['expansion'] = 'drums of war expansion';
				       break;
			       case 'grimlic\'s descent':
				       $row['expansion'] = 'grimlic\'s descent expansion';
				       break;
			       case 'path to conquest':
				       $row['expansion'] = 'path to conquest expansion';
				       break;
			       case 'crusade of the vashal legends i':
					$row['expansion'] = 'crusade of the vashal - legends i';
					break;
				   case 'crusade of the vashal legends ii':
					   $row['expansion'] = 'crusade of the vashal - legends ii';
					   break;
				   case 'endless wonder legends i':
					   $row['expansion'] = 'endless wonder - legends i';
					   break;
				   case 'endless wonder legends ii':
					   $row['expansion'] = 'endless wonder - legends ii';
					   break;
		       }
		       $row['expansion'] = stripslashes(str_replace("'", "", $row['expansion']));
		       $expansions[$row['expansion']] = $row['id'];
	       }
	       
	       //Selecting artist IDs and values from teh database to associate with runes
	       $query = "SELECT * FROM artists";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
				
				$artist = trim($row['first_name'] . ' ' . $row['last_name']);
		       $artists[$artist] = $row['id'];
	       }   
	       
	       
	       //Selecting classes from teh database to associate with runes
	       $query = "SELECT * FROM classes";
	       $results = mysql_query($query);
	       
	       while ($row = mysql_fetch_array($results))
	       {
		       $classes[$row['class']] = $row['id'];
	       }
	       
	       
	       //Selecting races from the database to associate with runes
	       $query = "SELECT * FROM races";
	       $results = mysql_query($query);
	       
	       while ($row = mysql_fetch_array($results))
	       {
		       $races[$row['race']] = $row['id'];
	       }
	       
	       
	       //Selecting rune IDs from the database to check for rune insertion
	       $query = "SELECT runes.id FROM runes";
	       $results = mysql_query($query);
	       
	       //Creating an array to hold all existing rune IDs
	       $existingRunes = array();
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       array_push($existingRunes, $row['id']);
	       }
	       
	       
	       //Preparing the top half of the rune insertion query
	       $baseQuery =
	       "
		       INSERT INTO
		       runes(id
			   , rune
			   , nora_cost
			   , type_id
			   , rarity_id
			   , artist_id
			   , expansion_id)
		       VALUES
	       ";
	       
	       //Preparing the top half of the champion insertion query
	       $championQuery =
	       "
		       INSERT INTO champions
		       (
			       rune_id,
			       hp,
			       speed,
			       defense,
			       damage,
			       min_range,
			       max_range,
			       size
		       )
		       VALUES
	       ";
	       
	       //Preparing the top half of the base ability insertion query
	       $baseAbilitiesQuery = "
		       INSERT INTO
		       champion_base_abilities(
			       rune_id,
			       ability_id
		       )
		       VALUES
	       ";
	       
	       //Preparing the top half of the upgrade_ability insertion query
	       $upgradeAbilitiesQuery =
	       "
		       INSERT INTO
		       champion_upgrade_abilities(
			       rune_id,
			       ability_id,
			       upgrade_slot
		       )
		       VALUES
	       ";
	       
	       $champFactionQuery =
	       "
		       INSERT INTO
		       rune_factions(
			       rune_id,
			       faction_id
		       )
		       VALUES
	       ";
	       
	       $classesQuery = "
		       INSERT INTO
		       champion_classes(
			       rune_id,
			       class_id
		       )
		       VALUES
	       ";
	       
	       $racesQuery = "
			       INSERT INTO
			       champion_races(
				       rune_id,
				       race_id
			       )
			       VALUES
		       ";
	       
	       //Creating a variable to keep track if any new champions are being inserted
	       $new = false;
	       
	       //Running through every champion in the XML    
	       foreach ($data['runes']['champions']['champ'] as $key => $champ)
	       {
		       //If the champion is already in the database, we skip them
		       if (in_array($champ['id'], $existingRunes))
		       {
			       continue;
		       }
		       
		       //Because the brute has inconsistent data, we skip him. General Korsien TWO was a problem also.
		       if ($champ['id'] == 56 || $champ['id'] == 829)
		       {
			       continue;
		       }
		       
		       $new = true;
		       
		       //Preparing champion data for comparison/insertion
		       $champ['artist'] = str_replace('"', '', trim(strtolower($champ['artist'])));
		       $champ['expansion'] = trim(str_replace("'", "", strtolower($champ['expansion'])));
		       $champ['name'] = trim(addslashes(strtolower($champ['name'])));
		       $champ['faction'] = trim(strtolower($champ['faction']));
		       $strRaces = trim(strtolower($champ['races']));
		       
		       //Concatenating the champion's data to the insertion query
		       $baseQuery .=	"(" . $champ['id'] .
				       ", '" . $champ['name'] .
				       "', " . $champ['nora'] .
				       ", 3" .
				       ", " . $champ['rarity'] .
				       ", " . $artists[$champ['artist']] .
				       ", " . $expansions[$champ['expansion']] .
				       "),";
		       
		       //Concatenating the champion's stats to the insertion query            
		       $championQuery .=
			       "(" . $champ['id'] .
			       ", " . $champ['hp'] .
			       ", " . $champ['spd'] .
			       ", " . $champ['def'] .
			       ", " . $champ['dmg'] .
			       ", " . $champ['minrng'] .
			       ", " . $champ['maxrng'] .
			       ", " . substr($champ['size'],0,1) .
			       "),";
			    
		       //Concatenating the champion base abilities to the insertion query   
		       foreach ($champ['starting_abilities']['ability'] as $key => $ability)
		       {
			       $baseAbilitiesQuery .= "(";
			       $baseAbilitiesQuery .= $champ['id'] . ", ";
			       $baseAbilitiesQuery .= $ability['id'];
			       $baseAbilitiesQuery .= "),";
		       }
		       
		       //Concatenating the champion upgrade abilities to the insertion query
		       $counter = 0;
		       foreach ($champ['upgrade_abilities']['ability'] as $key => $ability)
		       {
				$counter++;
			       $upgradeAbilitiesQuery .= "(";
			       $upgradeAbilitiesQuery .= $champ['id'] . ", ";
			       $upgradeAbilitiesQuery .= $ability['id'] . ", ";
			       $upgradeAbilitiesQuery .= $counter;
			       $upgradeAbilitiesQuery .= "),";
		       }
		       unset($counter);
		       
		       //Concatenating the champion faction to the insertion query
		       $champFactionQuery .=
			       "(" . $champ['id'] .
			       ", " . $factions[$champ['faction']] .
			       "),";
		       $xmlRuneFactions[$champ['id']] = $factions[$champ['faction']];
		       $strClasses = trim(strtolower($champ['classes']));
		       $spaceCount = substr_count($strClasses, ' ');
		       
		       if ($strClasses != 'none')
		       {
		       
			       //Creating an array to store the champion's classes
			       $champClasses = array();
			       
			       //If we have at least one space, we have more than one class
			       if ($spaceCount > 0)
			       {
				       //Locating the first space
				       $firstSpace = strpos($strClasses, ' ');
				       
				       
				       //Initializing comma points
				       $spacePos = $firstSpace;
				       $strRemaining = $strClasses;
			       
				       //Grabbing all of the classes
				       for ($i = 0; $i <= $spaceCount; $i++)
				       {
					       if ($spacePos == '')
					       {
						       $spacePos = strlen($strRemaining);
					       }
					       $class = trim(strtolower(substr($strRemaining, 0, $spacePos)));
					       
					       array_push($champClasses, $class);
				       
					       $strRemaining = substr($strRemaining, $spacePos + 1);
					       $spacePos = strpos($strRemaining, ' ');
				       }
			       }
			       //otherwise, we know we only have one class
			       else
			       {
				       $class = trim(strtolower($strClasses));
				       array_push($champClasses, $class);
			       }
			       
			       //Concatenating the champion's classes to the insertion query
			       foreach ($champClasses as $key => $class)
			       {
				       $classesQuery .= "(";
				       $classesQuery .= $champ['id'] . ", ";
				       $classesQuery .= $classes[$class];
				       $classesQuery .= "),";
			       }
		       }
		       if($strRaces != 'none')
		       {
			       //Counting the number of commas in the races string
			       $commaCount = substr_count($strRaces, ',');
			       
			       //Creating an array to store the champion's races
			       $champRaces = array();
			       
			       //If we have at least one comma, we have more than one race
			       if ($commaCount > 0)
			       {
				       $firstComma = strpos($strRaces, ',');
				       
				       //Initializing comma points
				       $commaPos = $firstComma;
				       $strRemaining = $strRaces;
			       
				       
				       
				       for ($i = 0; $i <= $commaCount; $i++)
				       {
					       if ($commaPos == '')
					       {
						       $commaPos = strlen($strRemaining);
					       }
					       $race = trim(strtolower(substr($strRemaining, 0, $commaPos)));
					       
					       array_push($champRaces, $race);
				       
					       $strRemaining = substr($strRemaining, $commaPos + 1);
					       $commaPos = strpos($strRemaining, ',');
				       }
			       }
			       else
			       {
				       $race = trim(strtolower($strRaces));
				       array_push($champRaces, $race);
			       }
			       
			       //Concatenating to the champion races insertion query
			       foreach ($champRaces as $key => $race)
			       {
				       $racesQuery .= "(";
				       $racesQuery .= $champ['id'] . ", ";
				       $racesQuery .= $races[$race];
				       $racesQuery .= "),";
			       }
		       }
		       
		       
       
		       
		       
	       }
	       
	       //Adding any rune factions which were previously in the database, but not in the XML (split runes)
	       foreach($existingRuneFactions as $runeID => $arrFactions)
	       {
		       if(!array_key_exists($runeID,$xmlRuneFactions))
			{
				continue;
			}
		       foreach($arrFactions as $key => $factionID)
		       {
			       
			       if($xmlRuneFactions[$runeID] != $factionID)
			       {
				       echo 'faction added... ' . $runeID . ' ' . $factionID . '<br />';
				      $champFactionQuery .=
				       "(" . $runeID .
				       ", " . $factionID .
				       "),"; 
			       }
		       }
	       }
	       
	       //Cutting off the last ',' from the insertion query
	       $baseQuery = substr($baseQuery, 0, -1);
	       //Chopping off the last ',' from the insertion query
	       $championQuery = substr($championQuery, 0, -1);
	       //Same..
	       $baseAbilitiesQuery = substr($baseAbilitiesQuery,0, -1);
	       //Cutting off the last ',' from our insertion string
	       $upgradeAbilitiesQuery = substr($upgradeAbilitiesQuery, 0, -1);
	       //again
	       $champFactionQuery = substr($champFactionQuery, 0, -1);
	       //and again
	       $classesQuery = substr($classesQuery,0, -1);
	       //yep
	       $racesQuery = substr($racesQuery,0, -1);
	       
	       //Printing the query to the screen so we can see what is going on
	       if (!$new)
	       {
		       echo '<p>There were no new champions to be inserted into the database.</p>';
	       }
	       else
	       {
		       echo '<p>Champion Base Rune Insertion Query:<br />' . $baseQuery . '</p>';
		       echo '<p>Champion Stat Insertion Query:<br />' . $championQuery . '</p>';
		       echo '<p>Champion Base Ability Insertion Query:<br />' . $baseAbilitiesQuery . '</p>';
		       echo '<p>Champion Upgrade Ability Insertion Query:<br />' . $upgradeAbilitiesQuery . '</p>';
		       echo '<p>Champion Faction Insertion Query:<br />' . $champFactionQuery . '</p>';
		       echo '<p>Champion Class Insertion Query:<br />' . $classesQuery . '</p>';
		       echo '<p>Champion Race Insertion Query:<br />' . $racesQuery . '</p>';
		       
		       //Executing our query and inserting all of the new abilities, if it is not a test
		       if (!$test)
		       {
			       mysql_query($baseQuery) or die(mysql_error());
			       mysql_query($championQuery) or die(mysql_error());
			       mysql_query($baseAbilitiesQuery) or die(mysql_error());
			       mysql_query($upgradeAbilitiesQuery) or die(mysql_error());
			       mysql_query($champFactionQuery) or die(mysql_error());
			       mysql_query($classesQuery) or die(mysql_error());
			       mysql_query($racesQuery) or die(mysql_error());
		       }
	       }
			       
       }
       
       
       
       
       function extract_runes($data, $test = true)
       {
	       $existingRuneFactions = $data['existingRuneFactions'];
	       $xmlRuneFactions = array();
	       
	       //Selecting rarity IDs and values from the database to associate with runes
	       $query = "SELECT * FROM rarities";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       $rarities[$row['rarity']] = $row['id'];
	       }
	       
	       
	       //Selecting faction IDs and values from the database to associate with runes
	       $query = "SELECT * FROM factions";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       $factions[$row['faction']] = $row['id'];
	       }
	       
	       
	       //Selecting expansion IDs and values from the database to asscoaite with runes
	       $query = "SELECT * FROM expansions";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       //exceptions, because I have made changes.
		       switch ($row['expansion'])
		       {
			       case 'poxnora release':
				       $row['expansion'] = 'poxnora release set';
				       break;
			       case 'savage tundra':
				       $row['expansion'] = 'savage tundra expansion';
				       break;
			       case 'shattered peaks':
				       $row['expansion'] = 'shattered peaks expansion';
				       break;
			       case 'drums of war':
				       $row['expansion'] = 'drums of war expansion';
				       break;
			       case 'grimlic\'s descent':
				       $row['expansion'] = 'grimlic\'s descent expansion';
				       break;
			       case 'path to conquest':
				       $row['expansion'] = 'path to conquest expansion';
				       break;
		       }
		       $row['expansion'] = stripslashes(str_replace("'", "", $row['expansion']));
		       $expansions[$row['expansion']] = $row['id'];
	       }
	       
	       //Selecting artist IDs and values from teh database to associate with runes
	       $query = "SELECT * FROM artists";
	       $results = mysql_query($query);
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       $artist = $row['first_name'] . ' ' . $row['last_name'];
		       $artists[$artist] = $row['id'];
	       }   
	       
	       //Selecting rune IDs from the database to check for rune insertion
	       $query = "SELECT runes.id FROM runes";
	       $results = mysql_query($query);
	       
	       //Creating an array to hold all existing rune IDs
	       $existingRunes = array();
	       
	       //Reading the results into an array for easy access
	       while ($row = mysql_fetch_array($results))
	       {
		       array_push($existingRunes, $row['id']);
	       }
	       
	       //Preparing the top half of the rune insertion query
	       $baseQuery =
	       "
		       INSERT INTO
		       runes(id
			   , rune
			   , nora_cost
			   , type_id
			   , rarity_id
			   , artist_id
			   , expansion_id)
		       VALUES
	       ";
	       
	       //Preparing the top half of the rune faction insertion query
	       $factionQuery =
	       "
		       INSERT INTO
		       rune_factions(
			       rune_id,
			       faction_id
		       )
		       VALUES
	       ";
	       
	       //Preparing the top half of the rune description query
	       $descriptionQuery = "
		       INSERT INTO
		       rune_descriptions
		       (
			       rune_id,
			       description
		       )
		       VALUES
	       ";
	       
	       //Simplifying our super-dimensional array, slightly
	       $runes['spells'] = $data['runes']['spells'];
	       $runes['equipment'] = $data['runes']['equipment'];
	       $runes['relics'] = $data['runes']['relics'];
	       
	       //Storing the singular form for loop purposes.
	       $runes['spells']['single'] = 'spell';
	       $runes['spells']['type'] = 4;
	       $runes['spells']['offset'] = 6000;
	       $runes['equipment']['single'] = 'equip';
	       $runes['equipment']['type'] = 1;
	       $runes['equipment']['offset'] = 4000;
	       $runes['relics']['single'] = 'relic';
	       $runes['relics']['offset'] = 8000;
	       $runes['relics']['type'] = 2;
	       
	       //Creating a variable to keep track if any new champions are being inserted
	       $new = false;
	       
	       //Running through every rune in the XML    
	       foreach($runes as $key => $category)
	       {
		       foreach ($category[$category['single']] as $key => $rune)
		       {
			       //If the rune is already in the database, we skip them
			       if (in_array(($rune['id'] + $category['offset']), $existingRunes))
			       {
				       continue;
			       }
			       
			       //Preparing rune data for comparison and insertion
			       $rune['expansion'] = trim(str_replace("'", "", strtolower($rune['expansion'])));
			       $rune['name'] = trim(addslashes(strtolower($rune['name'])));
			       $rune['faction'] = trim(strtolower($rune['faction']));
			       $rune['artist'] = trim(strtolower($rune['artist']));
			       $new = true;
			       
			       //Concatenating the runes's data to the insertion query
			       $baseQuery .=	"(" . ($category['offset'] + $rune['id']) .
					       ", '" . $rune['name'] .
					       "', " . $rune['nora'] .
					       ", " . $category['type'] .
					       ", " . $rune['rarity'] .
					       ", " . $artists[$rune['artist']] .
					       ", " . $expansions[$rune['expansion']] .
					       "),";
			       //Concatenating the rune's faction to the insertion query
			       $factionQuery .=
				       "(" . ($category['offset'] + $rune['id']) .
				       ", " . $factions[$rune['faction']] .
				       "),";
				//Keeping track of which factions were listed in the xml for runes
				$xmlRuneFactions[$category['offset'] + $rune['id']] = $factions[$rune['faction']];
				       
			       //Concatenating the rune's description to the insertion query
			       $descriptionQuery .= "(" . ($category['offset'] + $rune['id']) .
				       ", '" . trim(addslashes($rune['desc'])) .
				       "'),";
		       }
		     
	       }
	       echo '<pre>' . print_r($xmlRuneFactions) . '</pre>';
	       //Adding any rune factions which were previously in the database, but not in the XML (split runes)
	       foreach($existingRuneFactions as $runeID => $arrFactions)
	       {
		       if(!array_key_exists($runeID,$xmlRuneFactions))
			{
				continue;
			}
		       foreach($arrFactions as $key => $factionID)
		       {
			       if($xmlRuneFactions[$runeID] != $factionID)
			       {
				       echo 'faction added... ' . $runeID . ' ' . $factionID . '<br />';
				      $factionQuery .=
				       "(" . $runeID .
				       ", " . $factionID .
				       "),"; 
			       }
		       }
	       }
	       
	       //Trimming off trailing characters of queries
	       $baseQuery = substr($baseQuery, 0, -1);
	       $factionQuery = substr($factionQuery, 0, -1);
	       $descriptionQuery = substr($descriptionQuery, 0, -1);
	     
	     
	       //Printing the query to the screen so we can see what is going on
	       if (!$new)
	       {
		       echo '<p>There were no new runes to be inserted into the database.</p>';
	       }
	       else
	       {
		       echo '<p>Base Rune Insertion Query:<br />' . $baseQuery . '</p>';
		       echo '<p>Rune Faction Insertion Query:<br />' . $factionQuery . '</p>';
		       echo '<p>Rune Description Insertion Query:<br />' . $descriptionQuery . '</p>';
		       
		       //Executing our query and inserting all of the new abilities, if it is not a test
		       if (!$test)
		       {
			       mysql_query($baseQuery) or die(mysql_error());
			       mysql_query($factionQuery) or die(mysql_error());
			       mysql_query($descriptionQuery) or die(mysql_error());
			       
		       }
	       }
       }
       
       
       
	
	function update_abilities($data, $test = true)
	{		
		//Grabbing all existing abilities for comparison
		$existingAbilities = $this->Ability_model->select_all();
		
		//Simplifying our super-dimensional array a bit
		$champions = $data['runes']['champions'];
		
		//Creating an array to store all of abilities which have changed
		$abilities = array();
		
		//Array to store abilities as retrieved from the xml, for comparison
		$xmlAbilities = array();
		
		//Creating an array to keep track of anomalies in the XML
		$anomalies = array();
		
		//Creating an array to model the expected structure of the ability arrays
		$abilityModel = array
		(
			'id' => 0,
			'ap' => 0,
			'name' => 0,
			'desc' => 0,
			'type' => 0,
			'rank' => 0,
			'cooldown' => 0
		);
		
		//Retrieving all abilities within the xml
		foreach ($champions['champ'] as $key => $champ)
		{
			foreach($champ['starting_abilities']['ability'] as $key => $ability)
			{
				array_push($xmlAbilities, $ability);
			}
			
			foreach($champ['upgrade_abilities']['ability'] as $key => $ability)
			{
				array_push($xmlAbilities, $ability);
			}
		}
	       
		//Checking the abilities for differences
		foreach ($xmlAbilities as $key => $ability)
		{
			//Checking that the array structure is correct
			if(!array_structure_matches($ability, $abilityModel))
			{
				array_push($anomalies, $ability);
				continue;
			}
			
			//Checking if ability is even in the database yet
			if(!in_array($ability, $existingAbilities))
			{
				continue;
			}
			
			//Checking that the ability is not in the update array
			if (in_array($ability, $abilities))
			{
				continue;
			}
			
			//Checking that are fields are the same
			if( trim(strtolower($ability['name'])) != trim(strtolower($existingAbilities[$ability['id']]['ability'])))
			{
			       array_push($abilities, $ability);
			       continue;
			}
			
			if( trim($ability['desc']) != trim($existingAbilities[$ability['id']]['description']) )
			{
				array_push($abilities, $ability);
				continue;
			}
			
			if( trim($ability['rank']) != trim($existingAbilities[$ability['id']]['rank']) )
			{
				array_push($abilities, $ability);
				continue;
			}
			
			if( trim($ability['type']) != trim($existingAbilities[$ability['id']]['type']) )
			{
				array_push($abilities, $ability);
				continue;
			}
			
			if( trim($ability['ap']) != trim($existingAbilities[$ability['id']]['ap']) )
			{
				array_push($abilities, $ability);
				continue;
			}
			
			if( trim($ability['cooldown']) != trim($existingAbilities[$ability['id']]['cooldown']) )
			{
				array_push($abilities, $ability);
				continue;
			}
		}
	       
		//Adding all new abilities to the insertion string
		foreach ($abilities as $key => $ability)
		{
			//Removing extra white space, converting to lowercase, and escaping characters for the ability name
			$ability['name'] = trim(strtolower(mysql_real_escape_string($ability['name'])));
			
			//Escaping characters and removing any extra white space for the ability description
			$ability['desc'] = trim(mysql_real_escape_string($ability['desc']));
			
			//Creating our update query for each ability to be updated
			$query =
			"
				 UPDATE abilities
				 SET 
					 abilities.ability = '{$ability['name']}',
					 abilities.description = '{$ability['desc']}',
					 abilities.cooldown = {$ability['cooldown']},
					 abilities.rank = {$ability['rank']},
					 abilities.type = {$ability['type']},
					 abilities.ap = {$ability['ap']}
				 WHERE abilities.id = {$ability['id']}
			 ";
			 
			//Printing the query to the screen
			echo '<p>Ability Insertion Query:<br />' . $query . '</p>';
			
			//Executing our query and inserting all of the new abilities, if it is not a test
			if (!$test)
			{
				mysql_query($query) or die(mysql_error());
			}
			
		}
	       
	}
	
	
	
	/**
	 * xml2array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/xml2array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
	 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
	 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
	 */
	function xml2array($contents, $get_attributes=1, $priority = 'tag')
	{
	    if(!$contents) return array();
	
	    if(!function_exists('xml_parser_create')) {
		//print "'xml_parser_create()' function not found!";
		return array();
	    }
	
	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);
	
	    if(!$xml_values) return;//Hmm...
	
	    //Initializations
	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();
	
	    $current = &$xml_array; //Refference
	
	    //Go through the tags.
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	    foreach($xml_values as $data) {
		unset($attributes,$value);//Remove existing values, or there will be trouble
	
		//This command will extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array).
		extract($data);//We could use the array by itself, but this cooler.
	
		$result = array();
		$attributes_data = array();
		
		if(isset($value)) {
		    if($priority == 'tag') $result = $value;
		    else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		}
	
		//Set the attributes too.
		if(isset($attributes) and $get_attributes) {
		    foreach($attributes as $attr => $val) {
			if($priority == 'tag') $attributes_data[$attr] = $val;
			else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
		    }
		}
	
		//See tag status and do the needed.
		if($type == "open") {//The starting of the tag '<tag>'
		    $parent[$level-1] = &$current;
		    if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
			$current[$tag] = $result;
			if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
			$repeated_tag_index[$tag.'_'.$level] = 1;
	
			$current = &$current[$tag];
	
		    } else { //There was another element with the same tag name
	
			if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
			    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
			    $repeated_tag_index[$tag.'_'.$level]++;
			} else {//This section will make the value an array if multiple tags with the same name appear together
			    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
			    $repeated_tag_index[$tag.'_'.$level] = 2;
			    
			    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
				$current[$tag]['0_attr'] = $current[$tag.'_attr'];
				unset($current[$tag.'_attr']);
			    }
	
			}
			$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
			$current = &$current[$tag][$last_item_index];
		    }
	
		} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
		    //See if the key is already taken.
		    if(!isset($current[$tag])) { //New Key
			$current[$tag] = $result;
			$repeated_tag_index[$tag.'_'.$level] = 1;
			if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
	
		    } else { //If taken, put all things inside a list(array)
			if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
	
			    // ...push the new element into that array.
			    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
			    
			    if($priority == 'tag' and $get_attributes and $attributes_data) {
				$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
			    }
			    $repeated_tag_index[$tag.'_'.$level]++;
	
			} else { //If it is not an array...
			    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
			    $repeated_tag_index[$tag.'_'.$level] = 1;
			    if($priority == 'tag' and $get_attributes) {
				if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
				    
				    $current[$tag]['0_attr'] = $current[$tag.'_attr'];
				    unset($current[$tag.'_attr']);
				}
				
				if($attributes_data) {
				    $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
				}
			    }
			    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
			}
		    }
	
		} elseif($type == 'close') { //End of tag '</tag>'
		    $current = &$parent[$level-1];
		}
	    }
	    
	    return($xml_array);
	} 
}


?>
