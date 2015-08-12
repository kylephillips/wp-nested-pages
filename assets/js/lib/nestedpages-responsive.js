var NestedPages = NestedPages || {};

/**
* Responsive functionality for nested view
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Responsive = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.toggleEditButtons, function(e){
			e.preventDefault();
			plugin.toggleEdit($(this));
		});
		// Remove the block display when sizing up
		$(window).resize(function() {
			plugin.timer(function(){
				$('.action-buttons').removeAttr('style');
				$('.np-toggle-edit').removeClass('active');
			}, 500);
		});
	}

	// Toggle the responsive edit buttons
	plugin.toggleEdit = function(button)
	{
		var buttons = $(button).siblings('.action-buttons');
		if ( $(buttons).is(':visible') ){
			$(button).removeClass('active');
			$(buttons).hide();
			return;
		}
		$(button).addClass('active');
		$(buttons).show();
	}

	plugin.timer = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	return plugin.init();
}