<?php
if(isset($_POST['tradeLink']))
{
/*

	$message = 'link: ';
	$message .= $_POST['tradeLink'];
	
	mail('webmaster@poxpulse.com', 'Someone bought me a draft!', $message, 'From: webmaster@poxpulse.com');
	
	echo '<h1>Thank you!</h1>';
	echo '<p>I really appreciate, that you appreciate my work :). If you\'d like to play 
	the draft with me, send me an email at <a href="mailto:webmaster@poxpulse.com">webmaster@poxpulse.com</a>!</p>';
*/
}

else {
	
?>

<form action="" method="post">
	Trade Link: <input style="margin: 1em; width: 300px" type="text" name="tradeLink" id="tradeLink" /> <input type="submit" value="Submit" />
</form>

<h1>Show your Appreciation and buy Insomniac1 a Draft game!</h1>

<p>No really, I love to draft. Keep me interested in Poxnora and chances are I will continue to build features and 
maintain Pox Pulse.</p>

<p>I built and maintain Pox Pulse in my free time because I love Poxnora, and the community needed 
a quality database/fansite. I have spent many hours ensuring that you can search by race/class ect, 
and will no doubt spend many more adding new features and keeping the site updated.</p>

<p>If you would like to show your appreciation for my work, submit the link to your trade 
in the form above to gift me some draft tickets! I'll make a bid as Insomniac1. All 
those who buy me a draft game will be honored on Pox Pulse for supporting my habit!</p>
<?php } ?>


