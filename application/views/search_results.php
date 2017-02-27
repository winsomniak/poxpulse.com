<?php

        if (isset($iTotalResults) && $iTotalResults > 0)
        {
                echo '<h1>Search Results</h1>';

                echo '<div id="tabs">';
                        echo '<ul>';
                                if($iRunes > 0)
                                {
                                        echo '<li><a href="#runes"><span>Runes</span></a></li>';
                                }
                                if($iAbilities > 0)
                                {
                                        echo '<li><a href="#abilities"><span>Abilities</span></a></li>';
                                }
                        echo '</ul>';
                        echo '<div id="runes">';
                                
                        if (isset($iRunes) &&$iRunes > 0)
                        {
                                echo '<h2>Runes</h2>';
                                $headings = array('Rune', 'Type', 'Rarity', 'Expansion', 'Faction');
                                $items = array();
                                $factions = array();
                                
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
                                
                                $data['headings'] = $headings;
                                $data['items'] = $items;
                                
                                $this->load->view('table', $data);    
                        }
                        echo '</div>';
                
                        echo '<div id="abilities">';
                                if (isset($iAbilities) && $iAbilities > 0)
                                {
                                        echo '<h2>Abilities</h2>';
                                        
                                        $headings = array('Ability');
                                        $items = array();
                                        
                                        foreach ($abilities as $key => $ability)
                                        {
                                                
                                                array_push($items, '<a href="/ability/id/' . $ability['lowest_rank'] . '">' . ucwords($ability['ability']) . '</a><br />');
                                        }
                                        
                                        $data['headings'] = $headings;
                                        $data['items'] = $items;
                                        
                                        $this->load->view('table', $data);
                                }     
                        echo '</div>';
                echo '</div>';
                
                
                
        }
        else
        {
                echo 'Sorry, your search yielded no results.';
        }
        
?>