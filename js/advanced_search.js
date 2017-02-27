$(function() 
{
	//$('#advanced_search select').each(function(index, selectBox) 
	//{
	//	$(selectBox).combobox();
	//}
	//);

	$('#advanced_search input:submit').button();
	$('#advanced_search input:reset').button();
	$('#advanced_search input:reset').click(function(){
		$('#advanced_search option').attr('selected',false);
	});
	//$('#advanced_search input:text').button();
	$('#advanced_search select').combobox();
	$('.small-select-container .ui-combobox-input').addClass('small-combobox');
});