var NestedPages = NestedPages || {};

/**
* Settings
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Settings = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		postTypeToggle : '[data-toggle-nestedpages-pt-settings]', // Toggle Button for Post Type Settings
		postTypeCheckbox : '[data-nestedpages-pt-checbox]', // Checkbox for enabling post type
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.toggleAllSettingsButtons();
		});
		$(document).on('click', plugin.selectors.postTypeToggle, function(e){
			e.preventDefault();
			plugin.togglePostTypeSettings($(this));
		});
		$(document).on('change', plugin.selectors.postTypeCheckbox, function(){
			plugin.toggleSettingsButton($(this));
		});
	}

	/**
	* Toggle Individual Post Type Settings
	*/
	plugin.togglePostTypeSettings = function(button)
	{
		$(button).parent('.head').siblings('.body').toggle();
	}

	/**
	* Show/Hide the settings toggle button for enabled/disabled post types
	*/
	plugin.toggleSettingsButton = function(checkbox)
	{
		var button = $(checkbox).parents('.head').find(plugin.selectors.postTypeToggle);
		if ( $(checkbox).is(':checked') ){
			$(button).show();
			return;
		}
		$(button).hide();
		$(button).parents('.head').siblings('.body').hide();
		$(button).parents('.head').siblings('.body').find('input[type="checkbox"]').attr('checked', false);
	}

	/**
	* Toggle all the settings toggle buttons
	*/
	plugin.toggleAllSettingsButtons = function()
	{
		var checkboxes = $(plugin.selectors.postTypeCheckbox);
		$.each(checkboxes, function(){
			plugin.toggleSettingsButton($(this));
		});
	}

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	return plugin.init();
}

new NestedPages.Settings;