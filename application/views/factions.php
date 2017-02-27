


<?php
        echo '<h1>Factions</h1>';
        foreach($factions as $key => $faction)
        {
                $str = str_replace(' ', '-', $faction['faction']);
                $str = str_replace('\'', '', $str);
                echo '<a href="/search/faction/' . $str . '">' . ucwords($faction['faction']) . '</a><br />';
        }
        
?>