<?php $_SESSION['last_page'] = site_url();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>
	<title>Poxnora Database - Pox Pulse</title>
	<meta name="description" content="Pox Pulse is a thorough database of runes, abilities and all things related to Poxnora, the online collectible strategy game." />
        <link rel="icon" type="image/png" href="/favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="/jquery_ui_themes/dark-hive-sandpaper/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="/css/home.css" />
	<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.22.custom.min.js"></script>
	<script type="text/javascript" src="/js/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="/js/hoverIntent.js"></script>
	<script type="text/javascript" src="/js/superfish.js"></script>
	<script type="text/javascript" src="/js/main.js"></script>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />

	<?php $this->load->view('google_analytics'); ?>
</head>

<body>

<h1><img src="/images/logo_blue_500x120.png" alt="Poxnora Database - Pox Pulse" height="120" width="500" /></h1>

<div id="search">
	<form action="/search" method="post">
		<div>
			<input type="text" name="search_term" id="search_box" />
			<input type="submit" name="submit" id="submit" value="Search" />
		</div>
	</form>
</div>

<ul id="nav">
        <li class="top"><a href="/search/advanced" id="runes" class="top_link"><span class="down">Runes</span></a>
		<ul class="sub">
			<li><a href="/search/faction" id="factions" class="fly"><span >Factions</span></a>
				<ul>
					<li><a href="/search/faction/forglar-swamp">Forglar Swamp</a></li>
					<li><a href="/search/faction/forsaken-wastes">Forsaken Wastes</a></li>
					<li><a href="/search/faction/ironfist-stronghold">Ironfist Stronghold</a></li>
					<li><a href="/search/faction/kthir-forest">K'thir Forest</a></li>
					<li><a href="/search/faction/savage-tundra">Savage Tundra</a></li>
					<li><a href="/search/faction/shattered-peaks">Shattered Peaks</a></li>
					<li><a href="/search/faction/sundered-lands">Sundered Lands</a></li>
					<li><a href="/search/faction/underdepths">Underdepths</a></li>
				</ul>
			</li>
			<li><a href="/search/expansion" id="expansions" class="fly"><span >Expansions</span></a>
				<ul>
					<?php
					$expansions = $this->Search_model->search_expansions();
					
					foreach($expansions as $key => $expansion)
					{
						echo '<li><a href="/search/expansion/' . linkify($expansion['expansion']) . '">' . ucwords(str_replace('ii', 'II', str_replace('iii', 'III', $expansion['expansion']))) . '</a></li>';
					}
					?>
				</ul>
			</li>
			<li><a href="/rune/random" id="random"><span>Random Rune</span></a></li>
		</ul>
	</li>
	<li class="top"><a href="/ability" id="abilities" class="top_link"><span class="down">Ability List</span></a></li>
	<li class="top"><a href="/blog" id="blog" class="top_link"><span class="down">Blog</span></a></li>    
	<li class="top"><a href="/search/advanced" id="advanced_search" class="top_link"><span class="down">Advanced Search</span></a></li>
</ul>



<div id="message" class="ui-widget-content">
	<h3>Getting Started</h3>
	<p>Type a full or partial name of a rune or ability into the search box to get started</p>
	
	<p>Alternatively, you can navigate through the menu below the search box and browse.</p>
	
	<p>If you are looking for a more refined search you can use the advanced search feature,
	also located in the navigation menu below the search box.</p>
	
</div>
		
<div id="updates" class="ui-widget-content">
	<h3>Site Updates</h3>
	<em>February 10, 2016</em>
	<p>Poxpulse may see some updates in the near future as I am finishing up a complex project and need a data rich environment to test in.<br /><br />
	If I get the time, new features may include auto-updating of database (no more stale content), reddit style comment system on rune/ability pages, and a new site layout. Stay tuned.</p>
		
	
</div>

</body>
</html>

        
    
