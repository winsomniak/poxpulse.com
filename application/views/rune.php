<?php

$urlFaction = '/search/faction/';

//Determining the prefix for the background image and links
if (count($factions) > 1)
{
	$abbreviation = $factions[0]['abbreviation'] . '_' . $factions[1]['abbreviation'];
       
        $strFactions = '<a href="' . $urlFaction . linkify($factions[0]['faction']) . '">' . ucwords($factions[0]['faction']);
        $strFactions .= '</a> / <a href="' . $urlFaction . linkify($factions[1]['faction']) . '">' . ucwords($factions[1]['faction']) . '</a>';
}
else
{
	$abbreviation = $factions[0]['abbreviation'];
        $strFactions = '<a href="' . $urlFaction . linkify($factions[0]['faction']) . '">' . ucwords($factions[0]['faction']) . '</a>';
}
?>

<h1><?php echo ucwords($rune);?></h1>
<div id="right_col">
    <?php $this->load->view('block_ad');?>
    <div id="quick_facts" class="ui-widget-content">
        <h3>Quick Facts</h3>
        <ul>
		<li><strong>Cooldown:</strong> <?php echo floor($nora_cost / 5) . ' Rounds';?></li>
		<li><strong>Faction:</strong>  <?php echo $strFactions;?></li>
		<li><strong>Release Set:</strong> <?php echo '<a href="/search/expansion/' . linkify($expansion) . '">' . ucwords(str_replace('ii', 'II', str_replace('iii', 'III', $expansion)));?></a></li>
		<li><strong>Rarity:</strong>  <?php echo ucwords($rarity);?></li>
		<li><strong>Artist:</strong>  <?php echo ucwords($artist_first_name . ' ' . $artist_last_name);?></li>
        </ul>
            
    </div>
</div>
<div id="rune_container">
    <div id="rune">
        <?php
        $strImg = $rune;
        $strImg = str_replace(' ', '_', $strImg);
        $strImg = str_replace('-', '_', $strImg);
        $strImg = str_replace(',', '', $strImg);
        $strImg = str_replace('\'', '', $strImg);
        
        if ($rarity == 'limited edition')
        {
                $strImg .= '_limited_edition';
        }
        ?>
        <img src="/images/large/<?php echo $strImg;?>_270x310.jpg" width="270" height="310" alt="<?php echo ucwords($rune);?>" />
        <div class="frame" style="background: url(/images/large/frame/frames/<?php echo $abbreviation; ?>_310x390.gif)"></div>
        <div class="rarity" style="background-image: url(/images/large/frame/rarities/<?php echo str_replace(' ', '_', $rarity); ?>_310x390.gif)"></div>
        <?php
        if ($type == 'champion')
        {
            echo '<div class="stats">';
                echo '<div title="Damage">' . $damage . '</div>';
                echo '<div title="Speed">' . $speed . '</div>';
                
                if ($min_range === $max_range)
                {
                    $range = $min_range;
                }
                else
                {
                    $range = $min_range . '-' . $max_range;
                }
                echo '<div title="Range">' . $range . '</div>';
                echo '<div title="Defense">' . $defense . '</div>';
                echo '<div title="Hit Points">' . $hp . '</div>';
            echo '</div>';
        }
        ?>
        <div class="nora" title="Nora Cost"><?php echo $nora_cost; ?></div>
        <div class="name"><?php echo ucwords($rune);?></div>
        
    </div>
    <div id="rune_details">
        
        <div class="frame" style="background: url(/images/large/frame/frames/<?php echo $abbreviation; ?>_310x390.gif)"></div>
        <div class="nora" title="Nora Cost"><?php echo $nora_cost; ?></div>
        
        
        <?php
        if ($type == 'champion')
        {
            echo '<div class="name">Champion Details</div>';
            echo '<div class="background" style="background: url(/images/large/frame/backgrounds/' . $abbreviation . '_310x390.gif)"></div>';
            echo '<div class="champ_quote">' . $quote . '</div>';
            
            
            if (count($classes) > 0)
            {
                $strClasses = 'Class: <span class="runegreen">';
                foreach($classes as $key => $class)
                {
                    $strClasses .= '<a href="/search/advanced/class/' . $class['id'] . '/">' . ucwords($class['class']) . '</a>, ';
                }
                $strClasses = substr($strClasses, 0, -2);
                $strClasses .= '</span>';
            }
            else
            {
                $strClasses = 'Class: <span class="runegreen">None</span>';
            }
            
	    if (count($races) > 0)
	    {
		    $strRaces = 'Race: <span class="runegreen">';
		    foreach($races as $key => $race)
		    {
			$strRaces .= '<a href="/search/advanced/race/' . $race['id'] . '/">' . ucwords($race['race']) . '</a>, ';
		    }
		    $strRaces = substr($strRaces, 0, -2);
		    $strRaces .= '</span>';
	    }
	    else
	    {
		    $strRaces = 'Race: <span class="runegreen">None</span>';
	    }
            
            echo '<div class="races">' . $strRaces . '</div>';
            echo '<div class="classes">' . $strClasses . '</div>';
            
            echo '<div class="abilities">';
            foreach($baseAbilities as $key => $ability)
            {
                echo '<a title="' . ucwords($ability['ability']) . ' - ' . $ability['description'] . '" href="/ability/id/' . $ability['lowest_rank'] . '">';
                if ($ability['rank'] > 0)
                {
                        echo ucwords($ability['ability']) . ' ' . $ability['rank'];
                }
                else
                {
                        echo ucwords($ability['ability']);
                }
                echo '</a><br />';
            }
            echo '</div>';
            echo '<div class="upgrade_abilities">';
                foreach ($upgradeAbilities as $key => $ability)
                {
                    echo '<div>';
                        echo '<a href="/ability/id/' . $ability['lowest_rank'] . '">';
                        echo '<img title="' . ucwords($ability['ability']) . ' - ' . $ability['description'] . '" src="/images/icons/abilities/' . $ability['icon'] . '.gif" alt="' . ucwords($ability['ability']) . '" width="45" height="44" />';
                        echo '</a>';
                        if ($ability['rank'] > 0)
                        {
                                echo $ability['rank'];
                        }
                    echo '</div>';
                }
            echo '</div>';
        }
        else
        {
            echo '<div class="name">' . ucwords($rune) . '</div>';
            echo '<div class="background" style="background: url(/images/large/frame/backgrounds/default_310x390.gif)"></div>';
            echo '<div class="quote">' . $quote . '<hr />';
                echo '<div class="description">' . $description . '</div>';
            echo '</div>';
            
        }
        ?>
    </div>

</div>


<?php if(count($comments) > 0)
{
?>
<div id="tabs">
	<ul>
		<li><a href="#comments"><span>Comments</span></a></li>
	</ul>
	<div id="comments">
		
		<?php
		if (count($comments) > 0)
		{
			echo '<h2>Comments</h2>';
			//echo '<a href="javascript:void(0)" name="addComment">Add Comment</a>';
			foreach($comments as $key => $comment)
			{
				echo '<div>';
					echo '<p>' . $comment['user'] . ' on ' . date('m/d/y', $comment['date']) . ' at ' . date('g:i a', $comment['date']) . '</p>';
					echo '<p>' . nl2br($comment['comment']) . '</p>';
				echo '</div>';
				
			}
			
			//echo '<a href="javascript:void(0)" name="addComment">Add Comment</a>';
		}
		else
		{
			//echo 'Be the first to comment this page and <a href="javascript:void(0)" name="addComment">Add Comment</a>!';
		}
		?>
	</div>
	
</div>

<?php $this->load->view('add_comment.php');
}?>
