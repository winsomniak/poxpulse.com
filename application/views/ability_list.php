
<h1>Ability List</h1>

<?php

//Counting how many abilities there are to display
$iAbilityCount = count($abilities);
$iCol = ceil($iAbilityCount * .25);

//Creating the link string for each ability
foreach($abilities as $key => $ability)
{
    $abilities[$key]['str'] = '<a href="/ability/id/' . $ability['id'] . '">';
	$abilities[$key]['str'] .= ucwords($ability['ability']);
    $abilities[$key]['str'] .= '</a><br />';
}
?>
<div id="bottom_banner">
        <!--<a href="/appreciation"><img src="/images/buy_me_a_draft_728x90.png" alt="Buy Insomniac1 a Draft Game!" width="728" height="90" />--></a>
</div>
<?php
echo '<div id="abilities">';
	
	echo '<div class="col">';
		for($i = 0; $i < $iCol; $i++)
		{
			echo $abilities[$i]['str'];
		}
	echo '</div>';
	echo '<div class="col">';
		for($i = $iCol; $i < $iCol * 2; $i++)
		{
			echo $abilities[$i]['str'];
		}
	echo '</div>';
	echo '<div class="col">';
		for($i = $iCol * 2; $i < $iCol * 3; $i++)
		{
			echo $abilities[$i]['str'];
		}
	echo '</div>';
	echo '<div class="col">';
		for($i = $iCol * 3; $i < $iAbilityCount; $i++)
		{
			echo $abilities[$i]['str'];
		}
	echo '</div>';
echo '</div>';
?>      
