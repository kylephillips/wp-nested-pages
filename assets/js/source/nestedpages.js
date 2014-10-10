/**
* WP Pages Scripts Required by WP Pages Plugin
* @author Kyle Phillips
*/
jQuery(function($){

	/**
	* Add the Submenu Toggles (using JS to prevent additional DB Queries)
	*/
	$(document).ready(function(){
		add_remove_submenu_toggles();
	});
	
	/**
	* Toggle the Submenus
	*/
	$(document).on('click', '.child-toggle a', function(e){
		e.preventDefault();
		var submenu = $(this).parent('.child-toggle').parent('.row').siblings('ol');
		$(this).find('i').toggleClass('np-icon-arrow-down').toggleClass('np-icon-arrow-right');
		$(submenu).toggle();
	});

	/**
	* Toggle all pages
	*/
	$(document).on('click', '.nestedpages-toggleall a', function(e){
		e.preventDefault();
		if ( $(this).attr('data-toggle') == 'closed' )
		{
			$('.nestedpages ol li ol').show();
			$(this).attr('data-toggle', 'opened');
			$(this).text('Collapse Pages');
		} else
		{
			$('.nestedpages ol li ol').hide();
			$(this).attr('data-toggle', 'closed');
			$(this).text('Expand Pages');
		}
	});


	/**
	* Make the Menu sortable
	*/
	$(document).ready(function(){
		$('.sortable').nestedSortable({
			items : 'li',
			toleranceElement: '> .row',
			handle: '.handle',
			placeholder: "ui-sortable-placeholder",
			start: function(e, ui){
        		ui.placeholder.height(ui.item.height());
    		},
    		sort: function(e, ui){
    			update_placeholder_width(ui);
    		},
    		stop: function(e, ui){
    			setTimeout(
    				function(){
    					add_remove_submenu_toggles();
    			}, 100
    			);
    			submit_sortable_form();
    		},
    		update: function(e, ui){
    		}
		});
	});

	/**
	* Update the width of the placeholder
	*/
	function update_placeholder_width(ui)
	{
		var parentCount = $(ui.placeholder).parents('ol').length;
		var listWidth = $('.sortable').width();
		var offset = ( parentCount * 40 ) - 40;
		var newWidth = listWidth - offset;
		$(ui.placeholder).width(newWidth).css('margin-left', offset + 'px');
		update_list_visibility(ui);
	}

	/**
	* Make new list items visible
	*/
	function update_list_visibility(ui)
	{
		var parentList = $(ui.placeholder).parent('ol');
		if ( !$(parentList).is(':visible') ){
			$(parentList).show();
		}
	}


	/**
	* Add or Remove the submenu toggle after the list has changed
	*/
	function add_remove_submenu_toggles()
	{
		$('.child-toggle').each(function(i, v){
			var row = $(this).parent('.row').parent('li');

			if ( $(row).children('ol').length > 0 ){
				var icon = ( $(row).children('ol:visible').length > 0 ) ? 'np-icon-arrow-down' : 'np-icon-arrow-right';
				$(this).html('<a href="#"><i class="' + icon + '"></i></a>');
			} else {
				$(this).empty();
			}
		});
	}


	/**
	* Submit Sortable Form 
	* @todo add error div, pass message to it and show on error
	*/
	function submit_sortable_form()
	{
		$('#np-error').hide();
		$('#nested-loading').show();
		list = $('ol.sortable').nestedSortable('toHierarchy', {startDepthCount: 0});
		console.log(list);

		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : 'npsort',
				nonce : nestedpages.np_nonce,
				list : list
			},
			success: function(data){
				if (data.status === 'error'){
					$('#np-error').text(data.message).show();
					$('#nested-loading').hide();
				} else {
					$('#nested-loading').hide();
					console.log(data);
				}
			}
		});
	}


}); //$