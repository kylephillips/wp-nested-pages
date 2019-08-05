var NestedPages = NestedPages || {};

/**
* Formatting updates
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Formatter = function()
{
	
	var plugin = this;
	var $ = jQuery;


	// Update the Submenu Toggle Button State based on if the child menu is visible
	plugin.updateSubMenuToggle = function()
	{
		var allButtons = $(NestedPages.selectors.childToggle);
		for ( var i = 0; i < allButtons.length; i++ ){
			var button = allButtons[i];
			var row = $(button).parent('.row').parent('li');
			if ( $(row).children('ol').length > 0 ){ // Row has a child menu

				// Hide the toggle and child list if all items are in the trash
				if ( $(row).children('ol').find('li.page-row').length < 1 ){
					$(row).children('ol').hide();
					continue;
				}
				
				var open = ( $(row).children('ol:visible').length > 0 ) ? true : false;
				var html = '<div class="child-toggle-spacer"></div>';
				html += '<a href="#"';
				if ( open ) html += ' class="open"';
				html += '><span class="np-icon-arrow"></span></a>';
				$(button).html(html);

				if ( ($(row).children('ol').children('.np-hide').length > 0) && ($(row).children('ol').children('.np-hide.shown').length === 0) ){
					$(button).find('a').hide();
				} else if ( ($(row).children('ol').children('.np-hide').length > 0) && ($(row).children('ol').children('.np-hide.shown').length > 0) ){
					$(button).find('a').show();
				}

				continue;
			}
			$(button).empty().html('<div class="child-toggle-spacer"></div>'); // No Child Menu
		}
	}


	// Fix :visible :first css limitation when toggling various options
	plugin.setBorders = function()
	{
		$(NestedPages.selectors.rows).removeClass(NestedPages.cssClasses.noborder);
		$.each($(NestedPages.selectors.lists), function(){
			$(this).find('.page-row:visible:first').addClass(NestedPages.cssClasses.noborder);
		});
	}


	// Adjust nested margins based on how deep the list is nested
	plugin.setNestedMargins = function()
	{
		plugin.setIndent();
	}

	plugin.setIndent = function()
	{
		var amount = ( nestedpages.non_indent === '1' ) ? 20 : 30;
		var indent_element = ( nestedpages.non_indent === '1' ) ? '.row-inner' : '.child-toggle';
		$.each($(NestedPages.selectors.lists), function(i, v){
			var parent_count = $(this).parents(NestedPages.selectors.lists).length;
			var padding = 0;
			if ( !NestedPages.jsData.sortable ) padding = 10;
			if ( parent_count > 0 ){
				var padding = ( parent_count * amount ) + padding;
				$(this).find(indent_element).css('padding-left', padding + 'px');
				return;
			}
			if ( !NestedPages.jsData.sortable || $(this).hasClass('no-sort') ){
				$(this).find('.row-inner').css('padding-left', '10px');	
				return;
			}
			$(this).find('.row-inner').css('padding-left', '0px');
		});
	}

	plugin.setClassicIndent = function()
	{
		$.each($(NestedPages.selectors.lists), function(i, v){
			var parent_count = $(this).parents(NestedPages.selectors.lists).length;
			var padding = 0;
			if ( !NestedPages.jsData.sortable ) padding = 10;
			if ( parent_count > 0 ){
				var padding = ( parent_count * 20 ) + padding;
				$(this).find('.row-inner').css('padding-left', padding + 'px');
				return;
			}
			if ( !NestedPages.jsData.sortable || $(this).hasClass('no-sort') ){
				$(this).find('.row-inner').css('padding-left', '10px');	
				return;
			}
			$(this).find('.row-inner').css('padding-left', '0px');
		});
	}


	// Update the width of the placeholder ( width changes depending on level of nesting )
	plugin.updatePlaceholderWidth = function(ui)
	{
		if ( NestedPages.jsData.nestable ){
			var parentCount = $(ui.placeholder).parents('ol').length;
			var listWidth = $(NestedPages.selectors.sortable).width();
			var offset = ( parentCount * 40 ) - 40;
			var newWidth = listWidth - offset;
			$(ui.placeholder).width(newWidth).css('margin-left', offset + 'px');
		}
		plugin.updateListVisibility(ui);
	}


	// Update the list visibility on sort (prevent lists from collapsing when nesting)
	plugin.updateListVisibility = function(ui)
	{
		var parentList = $(ui.placeholder).parent('ol');
		if ( !$(parentList).is(':visible') ){
			$(parentList).addClass('nplist');
			$(parentList).show();
		}
	}


	// Remove the Quick Edit Overlay
	plugin.removeQuickEdit = function()
	{
		$(NestedPages.selectors.quickEditOverlay).removeClass('active').remove();
		$('.sortable .quick-edit').remove();
		$('.row').show();
	}


	// Show the Quick Edit Overlay
	plugin.showQuickEdit = function()
	{
		$('body').append('<div class="np-inline-overlay"></div>');
		setTimeout(function(){
			$('.np-inline-overlay').addClass('active');
		}, 50);
	}


	// Flash an updated row
	plugin.flashRow = function(row)
	{	
		$(row).addClass('np-updated');
		plugin.setBorders();
		setTimeout(function(){
			$(row).addClass('np-updated-show');
		}, 1500);
	}


	// Show an error message
	plugin.showAjaxError = function(message)
	{
		$(NestedPages.selectors.ajaxError).find('p').text(message);
		$(NestedPages.selectors.ajaxError).show();
	}


	// Hide the error message
	plugin.hideAjaxError = function(message)
	{
		$(NestedPages.selectors.ajaxError).hide();
	}


	// Size the link thumbnails to the same as the page/post thumbnails
	plugin.sizeLinkThumbnails = function()
	{
		var thumbnail = $(NestedPages.selectors.thumbnailContainer).not(NestedPages.selectors.thumbnailContainerLink).first().find('img');
		var width = $(thumbnail).width();
		var height = $(thumbnail).height();
		$.each($(NestedPages.selectors.thumbnailContainerLink), function(){
			$(this).width(width);
			$(this).height(height);
		});
	}

}