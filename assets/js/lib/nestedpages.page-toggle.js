var NestedPages = NestedPages || {};

/**
* Toggle Page Rows
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.PageToggle = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.toggleHidden, function(e){
			e.preventDefault();
			plugin.toggleHidden();
		});
		$(document).on('click', NestedPages.selectors.toggleStatus, function(e){
			e.preventDefault();
			plugin.toggleStatus($(this));
		});
	}


	// Toggle Hidden Pages
	plugin.toggleHidden = function()
	{
		var button = NestedPages.selectors.toggleHidden;
		var action = $(button).attr('href');
		if ( action === 'show' ){
			$(button).attr('href', 'hide').text(NestedPages.jsData.showHiddenText);
			$(NestedPages.selectors.hiddenRows).removeClass('shown').hide();
			plugin.formatter.setBorders();
			return;
		}
		$(button).attr('href', 'show').text(NestedPages.jsData.hideHiddenText);
		$(NestedPages.selectors.hiddenRows).addClass('shown').show();
		plugin.formatter.setBorders();
	}


	// Toggle Pages based on status
	plugin.toggleStatus = function(button)
	{
		var target = $(button).attr('href');
		$(NestedPages.selectors.toggleStatus).removeClass('active');
		$(button).addClass('active');
		if ( target == '#published' ){
			$(NestedPages.selectors.rows).hide();
			$(NestedPages.selectors.published).show();
			return;
		}
		$(NestedPages.selectors.rows).show();
	}


	return plugin.init();

}