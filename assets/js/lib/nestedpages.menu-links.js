var NestedPages = NestedPages || {};

/**
* Menu Item Selection in Modal Link Form
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuLinks = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		typeSelect : '[data-np-menu-item-selection]', // Select menu for type of menu link
		accordion : '[data-np-menu-accordion]', // Accordion of objects
		accordionItem : '[data-np-menu-accordion-item]', // Single item in the accordion
	}

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', plugin.selectors.accordionItem, function(e){
			e.preventDefault();
			plugin.accordion($(this));
		});
	}

	// Accordion Menu
	plugin.accordion = function(button)
	{
		var submenu = $(button).siblings('ul');
		if ( $(submenu).is(':visible') ){
			$(button).removeClass('active');
			$(submenu).slideUp('fast');
			return;
		}
		$(plugin.selectors.accordionItem).removeClass('active');
		$(button).addClass('active');
		$(button).parents(plugin.selectors.accordion).find('ul').slideUp('fast');
		$(submenu).slideDown('fast');
	}

	return plugin.init();
}