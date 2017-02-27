


<?php
        echo '<h1>Expansions</h1>';
        foreach($expansions as $key => $expansion)
        {
                $str = str_replace(' ', '-', $expansion['expansion']);
                $str = str_replace('\'', '', $str);
                echo '<a href="/search/expansion/' . $str . '">' . ucwords($expansion['expansion']) . '</a><br />';
        }
        
?>