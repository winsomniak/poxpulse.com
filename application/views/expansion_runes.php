


<?php
	//$this->load->view('block_ad');
        echo '<div id="expansion_header">';
			$this->load->view('block_ad');
				$expansion_unformatted = $expansion;
                $expansion = str_replace('ii', 'II', str_replace('iii', 'III', $expansion));
                echo '<h1>' . ucwords($expansion) . '</h1>';
                echo '<img src="/images/icons/expansions/' . str_replace('-', '_', linkify($expansion_unformatted)) . '.png" height="128" width="128" alt="' . ucwords(str_replace('ii', 'II', str_replace('iii', 'III', $expansion))) . '" />';
        echo '</div>';
        
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