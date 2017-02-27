<!DOCTYPE html>
<head>
	<title>Collection Images</title>
</head>
<body style="background-color: #000; color: #fff">
<img style="margin-left: 515px" src="http://www.poxpulse.com/images/collection/tickets.png" /><br />
<img style="margin-left: 515px" src="http://www.poxpulse.com/images/collection/collection1.png" /><br />
<?php 
for($i = 2; $i <= 14; $i++)
{
	if($i == 9)
	{
		echo '<img src="http://www.poxpulse.com/images/collection/filler1.png" /><br />';
	}
	echo '<img src="http://www.poxpulse.com/images/collection/collection' . $i . '.png" /><br />';
}
?>
</body>
</html>