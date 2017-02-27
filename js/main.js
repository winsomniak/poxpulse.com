$(document).ready(function()
{
	$("#search_box").focus();
	
	$("#clearRarity").click(function(){clearSelect("#rarity")});
	$("#clearType").click(function(){clearSelect("#type")});
	$("#advanced_search input:reset").click(function(){clearSelect("#rarity")});
	
	function clearSelect(selectBox)
	{
		$(selectBox).attr("selectedIndex", -1);
	}
	
    	$("#tabs").tabs();
	$("[class=dataTable]").dataTable({bFilter: true, bInfo : true,  bJQueryUI : true, bPaginate : false, oLanguage : { sSearch : "Filter: "}});
	var addCommentLinks = document.getElementsByName("addComment");
	
	for(var i = 0; i < addCommentLinks.length; i++)
	{
		addCommentLinks[i].onclick = addComment;
	}
	
	//If there is a dialog div on the page, we activate it.
	if($("#dialog"))
	{
		$("#dialog").dialog(
			{
			autoOpen: false,
			minWidth: 450,
			modal: true,
			draggable: false,
			resizable: false
			}
		);
		
		$('#dialog input:submit').button()
	}
	
	
	function addComment()
	{
		
		$("#dialog").dialog("open");
	}

});