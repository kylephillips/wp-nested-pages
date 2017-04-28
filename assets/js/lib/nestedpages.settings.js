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
		postTypeCheckbox : '[data-nestedpages-pt-checkbox]', // Checkbox for enabling post type
		customFieldsCheckbox : '[data-toggle-nestedpages-cf-settings]', // Checkbox for toggling custom fields settings
		standardFieldsCheckbox : '[data-toggle-nestedpages-sf-settings]', // Checkbox for toggling standard field settings
		taxonomiesFieldCheckbox : '[data-hide-taxonomies]', // Checkbox for disabling taxonomies from quick edit
		thumbnailsCheckbox : '[data-enable-thumbnails]', // Checkbox for enabling thumbnails in sort view
		menuEnabledOption : '[data-menu-enabled-option]', // Options when the menu is enabled
		disableMenuCheckbox : '[data-disable-menu-checkbox]', // Checkbox for disabling menus completely
		disableAutoCheckbox : '[data-menu-disable-auto-checkbox]', // Checkbox for disabling auto menu sync
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.toggleAllSettingsButtons();
			plugin.toogleAllFieldSettings('.custom-fields');
			plugin.toogleAllFieldSettings('.standard-fields');
			plugin.toggleMenuCheckboxes();
			plugin.toggleHideCheckbox();
		});
		$(document).on('click', plugin.selectors.postTypeToggle, function(e){
			e.preventDefault();
			plugin.togglePostTypeSettings($(this));
		});
		$(document).on('change', plugin.selectors.postTypeCheckbox, function(){
			plugin.toggleSettingsButton($(this));
		});
		$(document).on('change', plugin.selectors.customFieldsCheckbox, function(){
			plugin.toogleFieldSettings($(this), '.custom-fields');
		});
		$(document).on('change', plugin.selectors.standardFieldsCheckbox, function(){
			plugin.toogleFieldSettings($(this), '.standard-fields');
		});
		$(document).on('change', plugin.selectors.taxonomiesFieldCheckbox, function(){
			plugin.toggleTaxonomyCheckboxes($(this));
		});
		$(document).on('change', plugin.selectors.thumbnailsCheckbox, function(){
			plugin.toggleThumbnailSettings($(this));
		});
		$(document).on('change', plugin.selectors.disableMenuCheckbox, function(){
			plugin.toggleMenuCheckboxes();
		});
		$(document).on('change', plugin.selectors.disableAutoCheckbox, function(){
			plugin.toggleHideCheckbox();
		});
	}

	/**
	* Toggle Individual Post Type Settings
	*/
	plugin.togglePostTypeSettings = function(button)
	{
		$(button).parent('.head').siblings('.body').toggle();
		$(button).parents('.post-type').toggleClass('active');
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
		$(button).parents('.post-type').removeClass('active');
		$(button).parents('.head').siblings('.body').find('input[type="checkbox"]').attr('checked', false);
		$(button).parents('.head').siblings('.body').find('select').val(false);
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

	/**
	* Toggle Custom Field Choices
	*/
	plugin.toogleFieldSettings = function(checkbox, fieldGroupClass)
	{
		var choices = $(checkbox).parents('.body').find(fieldGroupClass);
		if ( $(checkbox).is(':checked') ){
			$(choices).show();
			return;
		}
		$(choices).hide();
	}

	/**
	* Toggle All the Custom Field Choices
	*/
	plugin.toogleAllFieldSettings = function(fieldGroupClass)
	{
		var checkboxes = $(plugin.selectors.customFieldsCheckbox);
		if ( fieldGroupClass == '.standard-fields' ){
			var checkboxes = $(plugin.selectors.standardFieldsCheckbox);
		}
		$.each(checkboxes, function(){
			plugin.toogleFieldSettings($(this), fieldGroupClass);
		});
	}

	/**
	* Toggle Taxonomy Checkboxes
	*/
	plugin.toggleTaxonomyCheckboxes = function(checkbox)
	{
		var choices = $(checkbox).parents('ul').find($('*[data-taxonomy-single]'));
		if ( $(checkbox).is(':checked') ){
			$(choices).hide();
			return;
		}
		$(choices).show();
	}

	/**
	* Toggle the Thumbnail Settings
	*/
	plugin.toggleThumbnailSettings = function(checkbox)
	{
		var settings = $(checkbox).parents('.field').find($('*[data-thumbnail-options]'));
		if ( $(checkbox).is(':checked') ){
			$(settings).show();
			return;
		}
		$(settings).hide();
	}

	/**
	* Toggle the Menu Checkboxes
	*/
	plugin.toggleMenuCheckboxes = function()
	{
		var checkbox = $(plugin.selectors.disableMenuCheckbox);
		var menuCheckboxes = $(plugin.selectors.menuEnabledOption);
		if ( $(checkbox).is(':checked') ){
			$(menuCheckboxes).hide();
			return;
		}
		$(menuCheckboxes).show();
	}

	/**
	* Toggle the hide checkbox option
	*/
	plugin.toggleHideCheckbox = function()
	{
		var checkbox = $(plugin.selectors.disableAutoCheckbox);
		var hideCheckboxOption = $('[data-menu-hide-checkbox]');
		if ( $(checkbox).is(':checked') ){
			$(hideCheckboxOption).hide();
			return;
		}
		$(hideCheckboxOption).show();
	}

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	return plugin.init();
}

new NestedPages.Settings;