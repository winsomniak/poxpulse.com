<?php
        echo '<h1>' . ucwords($abilities[0]['ability']) . '</h1>';
        echo '<div id="right_col">';
                $this->load->view('block_ad');
        echo '</div>';
        echo '<div id="left_col">';
        
        echo '<div id="ability">';
                echo '<img src="/images/icons/abilities/' . $abilities[0]['icon'] . '.gif" height="44" width="45" alt="' . ucwords($abilities[0]['ability']) . '" />';
                
                echo '<div id="ability_descriptions">';
                if (count($abilities) > 1)
                {
                         foreach ($abilities as $key => $ability)
                         {
                                echo '<div class="ui-widget-content">';
                                        echo '<strong>' . ucwords($ability['ability']) . ' - Rank ' . $ability['rank'] . '</strong><br />';
                                        echo '<p>' . $ability['description'] . '</p>';
                                        if ($ability['type'] == 0)
                                        {
                                                echo '<strong>Passive</strong>';
                                        }
                                        else
                                        {
                                                echo 'Cooldown: ' . $ability['cooldown'] . ' AP Cost: ' . $ability['ap'];
                                        }
                                        
                                echo '</div>';
                         }
                }
                else
                {
                         echo '<div class="ui-widget-content">';
                                echo '<strong>' . ucwords($abilities[0]['ability']) . '</strong><br />';
                                 echo '<p>' . $abilities[0]['description'] . '</p>';
                                 if ($abilities[0]['type'] == 0)
                                {
                                        echo '<strong>Passive</strong>';
                                }
                                else
                                {
                                        echo 'Cooldown: ' . $abilities[0]['cooldown'] . ' AP Cost: ' . $abilities[0]['ap'];
                                }
                        echo '</div>';
                }
                 echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
        echo '<div id="champions_with_ability">';
                echo '<h2>Champions with ' . ucwords($abilities[0]['ability']) . '</h2>';
                if (isset($champions))
                {
                        $headings = array('Champion', 'Faction');
                        $items = array();
                        $factions = array();
                        
                        foreach($champions as $key => $champion)
                        {
                                $strFactions = "";
                                unset($factions[1]);
                                
                                $factions[0] = $champion['faction_one'];
                                $factions[1] = $champion['faction_two'];
                                
                                if($champion['faction_two'] != NULL)
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
                                                
                                $strChampion = '<a href="/rune/id/' . $champion['id'] . '">' . ucwords($champion['rune']) . '</a>';
                                if($champion['rank'] > 0)
                                {
                                        $strChampion .= ' (Rank: ' . $champion['rank'] . ')';
                                }
                                
                                if(!in_array(array($strChampion, $strFactions), $items))
                                {
                                        array_push($items, array($strChampion, $strFactions));
                                }
                        }
                        $data['headings'] = $headings;
                        $data['items'] = $items;
                        $this->load->view('table', $data);
                }
        echo '</div>';
       
?>