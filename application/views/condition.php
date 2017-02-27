<?php
        echo '<h1>' . ucwords($condition['condition']) . '</h1>';
        echo '<div id="right_col">';
                $this->load->view('block_ad');
        echo '</div>';
        echo '<div id="left_col">';
        
        echo '<div id="condition">';
                echo '<img src="/images/icons/abilities/' . $condition['icon'] . '.gif" height="44" width="45" alt="' . ucwords($condition['condition']) . '" />';
                
                echo '<div id="description">';
                        echo $condition['description'];
                echo '</div>';
                
        echo '</div>';
        
        echo '</div>';
       
?>