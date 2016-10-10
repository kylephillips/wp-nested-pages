var NestedPages = NestedPages || {};

/**
* Bulk Actions for Nested View
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.BulkActions = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.bulkActionsCheckbox, function(){
			plugin.toggleBulkForm();
		});
	}

	/**
	* Toggle the Bulk Actions Form
	*/
	plugin.toggleBulkForm = function()
	{
		var checked = false;
		var checked_ids = '';
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				checked = true;
				checked_ids += $(this).val() + ',';
			}
		});
		if ( checked ){
			$(NestedPages.selectors.bulkActionsForm).show();
			$(NestedPages.selectors.bulkActionsIds).val(checked_ids);
			return;
		}
		$(NestedPages.selectors.bulkActionsIds).val('');
		$(NestedPages.selectors.bulkActionsForm).hide();
	}

	return plugin.init();
}