var NestedPages = NestedPages || {};

/**
* The Hidden Item Count for selected items in the Nested View
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.HiddenItemCount = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', NestedPages.selectors.bulkActionsCheckbox, function(){
			plugin.toggleHiddenCount();
		});
		$(document).on('click', NestedPages.selectors.toggleAll, function(){
			plugin.toggleHiddenCount();
		});
		$(document).on('click', NestedPages.selectors.pageToggle, function(){
			plugin.toggleHiddenCount();
		});
	}

	/**
	* Toggle the Hidden Count
	*/
	plugin.toggleHiddenCount = function()
	{
		var hiddenCount = 0;
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				if ( $(this).parent('div').hasClass('np-check-all') ) return;
				var row = $(this).closest('.page-row');
				if ( !$(row).is(':visible') ) hiddenCount++;
			}
		});
		if ( hiddenCount < 1 ){
			$(NestedPages.selectors.hiddenItemCountParent).hide();
			return;
		}
		$(NestedPages.selectors.hiddenItemCount).text(hiddenCount);
		$(NestedPages.selectors.hiddenItemCountParent).show();
	}

	return plugin.init();
}