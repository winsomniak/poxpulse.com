<?php

	$this->load->view('block_ad');
        echo '<div id="faction_header">';
                echo '<h1>' . ucwords($faction_data['faction']) . '</h1>';
                echo '<img src="/images/icons/factions/' . str_replace('-', '_', linkify($faction_data['faction'])) .
		 '_207x183.gif" alt="' . ucwords($faction_data['faction']) . '" width="207" height="183" />';
		echo '<p><strong>Split Bonus (15/15): </strong>' . $faction_data['split_bonus'] . '</p>';
		echo '<p><strong>Full Faction Bonus: </strong>' . $faction_data['full_bonus'] . '</p>';
        echo '</div>';
		//$this->load->view('banner_ad');
        
        $champions = array();
        $spells = array();
        $relics = array();
        $equipment = array();
        
        foreach($runes as $key => $rune)
        {
                switch ($rune['type']) {
                        case 'champion':
                                array_push($champions, $rune);
                                break;
                        case 'spell':
                                array_push($spells, $rune);
                                break;
                        case 'relic':
                                array_push($relics, $rune);
                                break;
                        case 'equipment':
                                array_push($equipment, $rune);
                                break;
                        default:
                                echo 'An error occurred with ' . $rune['rune'] . '.<br />';
                }
        }
        
        $championsPerColumn = count($champions) / 3;
        
        echo '<div id="runes">';
                echo '<div>';
                        echo '<h2>Champions</h2>';
                        for ($i = 0; $i <= $championsPerColumn; $i++)
                        {
                                echo '<a href="/rune/id/' . $champions[0]['id'] . '">' . ucwords($champions[0]['rune']) . '</a><br />';
                                $champions = array_slice($champions, 1);
                        }
                echo '</div>';
                
                echo '<div>';
                        echo '<h2>Champions</h2>';
                        for ($i = 0; $i <= $championsPerColumn; $i++)
                        {
                                echo '<a href="/rune/id/' . $champions[0]['id'] . '">' . ucwords($champions[0]['rune']) . '</a><br />';
                                $champions = array_slice($champions, 1);
                        }
                echo '</div>';
                
                echo '<div>';
                        echo '<h2>Champions</h2>';
                        for ($i = 0; $i <= $championsPerColumn && count($champions) > 0; $i++)
                        {
                                echo '<a href="/rune/id/' . $champions[0]['id'] . '">' . ucwords($champions[0]['rune']) . '</a><br />';
                                $champions = array_slice($champions, 1);
                        }
                echo '</div>';
                
                echo '<div>';
                        echo '<h2>Spells</h2>';
                        foreach($spells as $key => $rune)
                        {
                                echo '<a href="/rune/id/' . $rune['id'] . '">' . ucwords($rune['rune']) . '</a><br />';
                        }
                echo '</div>';
                
                echo '<div>';
                        echo '<div>';
                                echo '<h2>Relics</h2>';
                                foreach($relics as $key => $rune)
                                {
                                        echo '<a href="/rune/id/' . $rune['id'] . '">' . ucwords($rune['rune']) . '</a><br />';
                                }
                        echo '</div>';
                        
                        echo '<div>';
                                echo '<h2>Equipment</h2>';
                                foreach($equipment as $key => $rune)
                                {
                                        echo '<a href="/rune/id/' . $rune['id'] . '">' . ucwords($rune['rune']) . '</a><br />';
                                }
                        echo '</div>';
                echo '</div>';
        echo '</div>';
                
                
        
?>