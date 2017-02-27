<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>
	<title>
		<?php
		if (isset($title))
		{
				echo ucwords(str_replace('ii', 'II', str_replace('iii', 'III', $title))) . ' - ';
		}
		?>
		Poxnora Database - Pox Pulse
    </title>
    
    <link rel="icon" type="image/png" href="/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/jquery_ui_themes/dark-hive-sandpaper/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="/css/main.css" />
	<link rel="stylesheet" type="text/css" href="/css/table.css" />
	<link rel="stylesheet" type="text/css" href="/css/nav.css" />
        
	<?php 
	
	//Loading up any css pages specific to the page
	if (isset($css_files))
	{
			foreach($css_files as $key => $file)
			{
					echo '<link rel="stylesheet" type="text/css" href="/css/' . $file . '" />';
			}
	}
	
	//Loading js files
	?>
	
	<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.22.custom.min.js"></script>
	<script type="text/javascript" src="/js/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="/js/hoverIntent.js"></script>
	<script type="text/javascript" src="/js/superfish.js"></script>
	<script type="text/javascript" src="/js/main.js"></script>
	
	
	<?php
	if (isset($js_files))
	{
			foreach($js_files as $key => $file)
			{
					echo '<script type="text/javascript" src="/js/' . $file . '"></script>';
			}
	}
	
	$_SESSION['last_page'] = current_url();
	
	if (isset($description))
	{
		echo '<meta name="description" content="' . $description . '" />' . "\r\n";
	}
	
	?>
        
	
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />

	<?php
	$this->load->view('google_analytics');
	
	if(isset($ads))
	{
		$this->load->view('adsense');	
	}
	?>


</head>

<body>

<div id="container">

    <div id="logo">
		<a href="/">
                        <img src="/images/logo_blue_500x120.png" height="120" width="500" alt="Poxnora Database - Pox Pulse" title="Poxnora Database - Pox Pulse" />
                </a>
    </div>
            
    <div id="main">
        <?php $this->load->view('nav');?>
            <div id="content">
