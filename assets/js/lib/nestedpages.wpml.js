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

	plugin.selectors = {
		translationsBtn : 'data-nestedpages-translations'
	}

	plugin.bindEvents = function()
	{
		if ( !nestedpages.wpml ) return;
		$(document).on('click', '[' + plugin.selectors.translationsBtn + ']', function(e){
			e.preventDefault();
			plugin.openTranslationsModal($(this));
		});
	}

	/**
	* Open the modal window displaying translation options for the selected page
	*/
	plugin.openTranslationsModal = function(button)
	{
		console.log(button);
	}

	return plugin.bindEvents();
}