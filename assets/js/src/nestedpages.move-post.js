var NestedPages = NestedPages || {};

/**
* Move a Post Up or Down in the list
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MovePost = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.nesting = new NestedPages.Nesting;
	plugin.activeRow;

	plugin.selectors = {
		moveToTop : 'data-push-to-top',
		moveToBottom : 'data-push-to-bottom'
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.disableTopOnFirst();
			plugin.disableBottomOnLast();
		});
		$(document).on('click', '[' + plugin.selectors.moveToTop + ']', function(e){
			e.preventDefault();
			if ( $(this).hasClass('disabled') ) return;
			plugin.activeRow = $(this).closest(NestedPages.selectors.rows);
			plugin.moveToTop();
		});
		$(document).on('click', '[' + plugin.selectors.moveToBottom + ']', function(e){
			e.preventDefault();
			if ( $(this).hasClass('disabled') ) return;
			plugin.activeRow = $(this).closest(NestedPages.selectors.rows);
			plugin.moveToBottom();
		});
	}

	/**
	* Move a post to the top of its list
	*/
	plugin.moveToTop = function()
	{
		var parent = $(plugin.activeRow).parent(NestedPages.selectors.lists);
		var first = $(parent).find(NestedPages.selectors.rows).first();
		$(plugin.activeRow).insertBefore(first);
		plugin.formatter.setBorders();
		$(document).click(); // Close Dropdowns
		plugin.nesting.syncNesting();
		plugin.disableTopOnFirst();
		plugin.disableBottomOnLast();
	}

	/**
	* Move a post to the bottom of its list
	*/
	plugin.moveToBottom = function()
	{
		var parent = $(plugin.activeRow).parent(NestedPages.selectors.lists);
		var last = $(parent).children(NestedPages.selectors.rows).last();
		$(plugin.activeRow).insertAfter(last);
		plugin.formatter.setBorders();
		$(document).click(); // Close Dropdowns
		plugin.nesting.syncNesting();
		plugin.disableTopOnFirst();
		plugin.disableBottomOnLast();
	}

	plugin.disableTopOnFirst = function()
	{
		var lists = $(NestedPages.selectors.lists);
		$.each(lists, function(){
			$(this).find('[' + plugin.selectors.moveToTop + ']').removeClass('disabled');
			var first = $(this).find(NestedPages.selectors.rows).first();
			$(first).find('[' + plugin.selectors.moveToTop + ']').addClass('disabled');
		});
	}

	plugin.disableBottomOnLast = function()
	{
		var lists = $(NestedPages.selectors.lists);
		$.each(lists, function(){
			$(this).find('[' + plugin.selectors.moveToBottom + ']').removeClass('disabled');
			var last = $(this).find(NestedPages.selectors.rows).last();
			$(last).find('[' + plugin.selectors.moveToBottom + ']').addClass('disabled');
		});
	}

	return plugin.bindEvents();
}