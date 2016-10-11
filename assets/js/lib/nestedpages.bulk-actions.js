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

	plugin.selectedNumber = 0;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', NestedPages.selectors.bulkActionsCheckbox, function(){
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
			plugin.setSelectedNumber();
			return;
		}
		$(NestedPages.selectors.bulkActionsIds).val('');
		$(NestedPages.selectors.bulkActionsForm).hide();
	}

	/**
	* Set the number of total selected
	*/
	plugin.setSelectedNumber = function()
	{
		var checkedLength = $(NestedPages.selectors.bulkActionsCheckbox + ':checked').length;
		var option = $(NestedPages.selectors.bulkActionsForm).find('select option').first();
		$(option).text(nestedpages.bulk_actions + ' (' + checkedLength + ')');
		console.log(checkedLength);
	}

	return plugin.init();
}