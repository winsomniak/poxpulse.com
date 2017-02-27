<?php
echo '<form action="/search/advanced" method="post">';
echo '<div id="advanced_search">';

	echo '<div>';
		$this->load->view('block_ad');
		echo '<div>';
			echo '<h2>Champion Specific</h2>';
			echo '<table>';
				echo '<tr>';
					echo '<th>Race:</th><td><select name="race">';
						echo '<option value=""></option>';
						foreach($races as $key => $race)
						{
							echo '<option value="' . $race['id'] . '"';
							if( isset($fields['race']) && $fields['race'] == $key)
							{
									echo ' selected="selected"';
							}
							echo '>' . ucwords($race['race']) . '</option>';
						}
					echo '</select></td>';
				echo '</tr>';
				
				echo '<tr>';
				echo '<th>Class:</th><td><select name="class">';
					echo '<option value=""></option>';
					foreach($classes as $key => $class)
					{
						echo '<option value="' . $class['id'] . '"';
						if( isset($fields['class']) && $fields['class'] == $key)
						{
								echo ' selected="selected"';
						}
						echo '>' . ucwords($class['class']) . '</option>';
					}
					echo '</select></td>';
					echo '</tr>';
			echo '</table>';
		echo '</div>';
	echo '</div>';
	
	
	echo '<h1>Advanced Rune Search</h1>';
	echo '<table>';
		echo '<tr>';
			echo '<th>Rune:</th><td><input type="text" id="rune" name="rune" ';
			if (isset($fields['rune']))
			{
				echo 'value="' . $fields['rune'] . '" ';
			}
			echo '/></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>Faction(s):</th>';
			echo '<td><select name="faction1">';
				echo '<option value=""></option>';
				foreach($factions as $key => $faction)
				{
					echo '<option value="' . $faction['id'] . '"';
					if( isset($fields['faction1']) && $fields['faction1'] == $key)
					{
							echo ' selected="selected"';
					}
					echo '>' . ucwords($faction['faction']) . '</option>';
				}
			echo '</select></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="small-select-container">';
				echo '<select name="flogic">';
					echo '<option value="or">OR</option><option value="and"';
					if( isset($fields['flogic']) && $fields['flogic'] == 'and')
					{
						echo ' selected="selected"';
					}
					echo '>AND</option>';
				echo '</select>';
			echo '</td>';
			echo '<td><select name="faction2">';
				echo '<option value=""></option>';
				foreach($factions as $key => $faction)
				{
					echo '<option value="' . $faction['id'] . '"';
					if( isset($fields['faction2']) && $fields['faction2'] == $key)
					{
							echo ' selected="selected"';
					}
					echo '>' . ucwords($faction['faction']) . '</option>';
				}
			echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>Type:</th>';
			echo '<td><select id="type" name="type" size="4">';
			foreach($types as $key => $type)
			{
				echo '<option value="' . $type['id'] . '"';
				if( isset($fields['type']) && $fields['type'] == $key)
				{
						echo ' selected="selected"';
				}
				echo '>' . ucwords($type['type']) . '</option>';
			}
		echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>Rarity:</th>';
			echo '<td><select id="rarity" name="rarity" size="6">';
				foreach($rarities as $key => $rarity)
				{
					echo '<option value="' . $rarity['id'] . '"';
					if( isset($fields['rarity']) && $fields['rarity'] == $key)
					{
						echo ' selected="selected"';
					}
					echo '>' . ucwords($rarity['rarity']) . '</option>';
				}
			echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>Expansion:</th>';
			echo '<td><select name="expansion">';
				echo '<option value=""></option>';
				foreach($expansions as $key => $expansion)
				{
					echo '<option value="' . $expansion['id'] . '"';
					if( isset($fields['expansion']) && $fields['expansion'] == $key)
					{
						echo ' selected="selected"';
					}
					echo '>' . ucwords($expansion['expansion']) . '</option>';
				}
			echo '</select></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th></th>';
			echo '<td><input type="submit" value="Search" name="submit" /> <input type="reset" value="Reset" name="reset" /> </td>';
		echo '</tr>';
		
		
		/* Works, but needs validation
		echo 'Nora Cost: ';
		echo ' <input type="text" name="noramin" ';
		if(isset($fields['noramin']))
		{
			echo 'value="' . $fields['noramin'] . '"';
		}
		echo '/> - <input type="text" name="noramax"';
		if(isset($fields['noramax']))
		{
			echo 'value="' . $fields['noramax'] . '"';
		}
		echo '/><br />';
		*/
	
	echo '</table>';
	
	
echo '</div>';        
	
echo '</form>';
      


	if (isset($iRunes) && $iRunes > 0)
	{
		echo '<br /><h1>Search Results</h1>';
		//echo '<div id="tabs" style="margin-top: 1em;">'; tabs disabled
			/*echo '<ul>';
			if($iRunes > 0)
			{
				echo '<li><a href="#runes"><span>Runes</span></a></li>';
			}
				
			echo '</ul>';
			*/
			echo '<div id="runes">';
			  
			
			//echo '<h2>Runes</h2>';
			$headings = array('Rune', 'Type', 'Rarity', 'Expansion', 'Faction');
			$items = array();
			$factions = array();
			
			//Setting up the data to send to the dataTable for each rune
			foreach($runes as $key => $rune)
			{
				$strFactions = "";
				unset($factions[1]);
				
				$factions[0] = $rune['faction_one'];
				$factions[1] = $rune['faction_two'];
				
				if($rune['faction_two'] != NULL)
				{
					sort($factions);
					
					$strFactions =
					 '<img src="/images/icons/factions/' . str_replace('-', '_', linkify($factions[0])) . '_23x20.gif" width="23" height="20" alt="' . ucwords($factions[0]) . '" />' . 
					 '<a href="/search/faction/' . linkify($factions[0]) . '">' . ucwords($factions[0]) .
					 '</a>, <img src="/images/icons/factions/' . str_replace('-', '_', linkify($factions[1])) . '_23x20.gif" width="23" height="20" alt="' . ucwords($factions[1]) . '" />' .
					 '<a href="/search/faction/' . linkify($factions[1]) . '">' . ucwords($factions[1]) . '</a>';
				}
				else
				{
					$strFactions =
					'<img src="/images/icons/factions/' . str_replace('-', '_', linkify($factions[0])) . '_23x20.gif" width="23" height="20" alt="' . ucwords($factions[0]) . '" />' .
					'<a href="/search/faction/' . linkify($factions[0]) . '">' . ucwords($factions[0]) . '</a>';
				}
				
				$strExpansion = '<a href="/search/expansion/' . linkify($rune['expansion']) . '">' . ucwords(str_replace('ii', 'II', $rune['expansion'])) . '</a>';
				$strRune = '<a href="/rune/id/' . $rune['id'] . '">' . ucwords($rune['rune']) . '</a>';
				array_push($items, array($strRune, ucwords($rune['type']), ucwords($rune['rarity']), $strExpansion, $strFactions));
			}
			
			//Packaging the headings and items to be sent to the table
			$data['headings'] = $headings;
			$data['items'] = $items;
			
			//Loading the table
			$this->load->view('table', $data);    
			echo '</div>';
		//echo '</div>'; tabs disabled
	}			
	
	else if (isset($runes) && $iRunes < 1)
	{
		echo 'Sorry, your search yielded no results.';
	}
	  
    
?>