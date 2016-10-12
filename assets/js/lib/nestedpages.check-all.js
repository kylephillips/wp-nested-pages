var NestedPages = NestedPages || {};

/**
* Check All functionality for Nested Pages
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.CheckAll = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeCheckbox = "";

	plugin.selectors = {
		checkbox : '[data-np-check-all]',
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', plugin.selectors.checkbox, function(){
			plugin.activeCheckbox = $(this);
			plugin.toggleCheckboxes();
		});
		$(document).on('change', 'input[type=checkbox]', function(){
			plugin.checkAllStatus($(this));
		});
	}

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.toggleCheckboxes = function()
	{
		var checked = ( $(plugin.activeCheckbox).is(':checked') ) ? true : false;
		var name = $(plugin.activeCheckbox).attr('data-np-check-all');

		var checkboxes = $('*[name="' + name + '"]');
		$.each(checkboxes, function(){
			var row = $(this).parents(NestedPages.selectors.rows);
			$(this).prop('checked', checked);
			// Uncheck any hidden checkboxes
			if ( $(row).hasClass('np-hide') && !$(row).is(':visible') ) {
				$(row).find(NestedPages.selectors.bulkActionsCheckbox).prop('checked', false)
			};
		});

		plugin.toggleCheckAll();
	}

	/**
	* Toggle the "Partial" class for the checkall checkbox
	*/
	plugin.toggleCheckAll = function()
	{
		var name = $(plugin.activeCheckbox).attr('data-np-check-all');
		
		var checkboxes_total = $('*[name="' + name + '"]').length;
		var hidden_checkboxes = $('.np-hide').find(NestedPages.selectors.bulkActionsCheckbox).length;
		var hidden_checkboxes_visible = $('.np-hide:visible').find(NestedPages.selectors.bulkActionsCheckbox).length;

		checkboxes_total = ( checkboxes_total - hidden_checkboxes ) + hidden_checkboxes_visible;
		
		var checkboxes_checked = $('*[name="' + name + '"]:checked').length;

		if ( checkboxes_total == checkboxes_checked ){
			$(plugin.activeCheckbox).prop('checked', true);
			$(plugin.activeCheckbox).removeClass('check-all-partial');
			return;
		}
		if ( checkboxes_checked > 0 ){
			$(plugin.activeCheckbox).addClass('check-all-partial');
			return;
		}
		$(plugin.activeCheckbox).attr('checked', false);
		$(plugin.activeCheckbox).removeClass('check-all-partial');
	}

	plugin.checkAllStatus = function(checkbox)
	{
		var name = $(checkbox).attr('name');
		var toggleAllCheckbox = $('*[data-np-check-all="' + name + '"]');
		if ( toggleAllCheckbox.length === 0 ) return;
		plugin.activeCheckbox = $(toggleAllCheckbox)[0];
		plugin.toggleCheckAll();
	}

	return plugin.init();
}