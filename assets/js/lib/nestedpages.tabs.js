var NestedPages = NestedPages || {};

/**
* Tab functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Tabs = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeContent = '';
	plugin.activeButton = '';

	plugin.init = function()
	{
		plugin.bindEvents();
	}


	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.tabButton, function(e){
			e.preventDefault();
			plugin.activeButton = $(this);
			plugin.toggleTabs();
		});
	}


	plugin.toggleTabs = function()
	{
		plugin.activeContent = $(plugin.activeButton).attr('href');
		$(NestedPages.selectors.tabContent).hide();
		$(plugin.activeContent).show();
		$(plugin.activeButton).parents(NestedPages.selectors.tabButtonParent).find(NestedPages.selectors.tabButton).removeClass('active');
		$(plugin.activeButton).addClass('active');
	}

	return plugin.init();
}