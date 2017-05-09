var NestedPages = NestedPages || {};

/**
* WPML functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Wpml = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.bindEvents = function()
	{
		if ( !nestedpages.wpml ) return;
		console.log('WPML installed');
	}

	return plugin.bindEvents();
}