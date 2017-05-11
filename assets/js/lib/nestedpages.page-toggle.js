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

	plugin.activeButton = '';

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.toggleHidden, function(e){
			e.preventDefault();
			plugin.activeButton = $(this);
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
		var button = $(plugin.activeButton);
		var action = $(button).attr('href');

		if ( action == '#show' ){
			$(button).attr('href', '#hide').text(NestedPages.jsData.showHiddenText);
			$(NestedPages.selectors.hiddenRows).removeClass('shown').hide();
			plugin.formatter.updateSubMenuToggle();
			plugin.formatter.setBorders();
			return;
		}

		if ( action == '#hide' ){
			$(button).attr('href', '#show').text(NestedPages.jsData.hideHiddenText);
			$(NestedPages.selectors.hiddenRows).addClass('shown').show();
			plugin.formatter.updateSubMenuToggle();
			plugin.formatter.setBorders();
		}
	}


	// Toggle Pages based on status
	plugin.toggleStatus = function(button)
	{
		var target = $(button).attr('href');
		target = target.substring(1);
		$(NestedPages.selectors.syncCheckbox).attr('disabled', false);
		$(NestedPages.selectors.toggleStatus).removeClass('active');
		$(button).addClass('active');
		if ( target == 'draft' ){
			$(NestedPages.selectors.syncCheckbox).attr('disabled', true);
			$('.' + target).addClass('nested-visible');
		}
		if ( target == 'all' ){
			$(NestedPages.selectors.rows).show();
			return;
		}
		$(NestedPages.selectors.rows).hide();
		$('.' + target).show();
		return;
	}


	return plugin.init();

}