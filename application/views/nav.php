<div id="nav">
	<ul id="nav" class="sf-menu">
		<li class="current first-link" >
			<a href="/search/faction" id="factions">Factions</a>
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
		
		<li class="current">
			<a href="/search/expansion" id="expansions">Expansions</a>
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
		<li class="current"><a href="/ability" id="abilities">Ability List</a></li>
		<li class="current"><a href="/blog" id="blog">Blog</a></li>
		<li class="current"><a href="/search/advanced">Advanced Search</a></li>
	</ul>
	<div id="search">
		<form action="/search" method="post">
			<div>
				<input type="text" name="search_term" id="search_box" />
				<input type="submit" name="submit" id="submit" value="Search" />
			</div>
		</form>          
	</div>
</div>
<script>
jQuery(function(){
	jQuery('ul.sf-menu').superfish();
});
</script>
		
