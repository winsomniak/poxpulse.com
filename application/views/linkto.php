<?php

//Code taken from a forum to give current url
$host = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];
$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
$url = !empty($query) ? "http://$host$self?$query" : "http://$host$self";

if(isset($rune['rune']))
{
	$subject = $rune['rune'];
}
else if(isset($ability['ability']))
{
	$subject = $ability['ability'];
}
else
{
	$subject = $url;
}

$html = '<a href="' . $url . '">' . $subject . '</a>';
$static = $url;
?>

<div id="linkto" title="Link This Page">
	<p><?php echo $html;?></p>
	<p><?php echo $static;?></p>
		
</div>
