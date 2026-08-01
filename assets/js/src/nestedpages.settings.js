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
		postTypeCheckbox : '[data-nestedpages-settings-row-checkbox]', // Checkbox for enabling post type
		customFieldsCheckbox : '[data-toggle-nestedpages-cf-settings]', // Checkbox for toggling custom fields settings
		standardFieldsCheckbox : '[data-toggle-nestedpages-sf-settings]', // Checkbox for toggling standard field settings
		taxonomiesFieldCheckbox : '[data-hide-taxonomies]', // Checkbox for disabling taxonomies from quick edit
		thumbnailsCheckbox : '[data-enable-thumbnails]', // Checkbox for enabling thumbnails in sort view
		menuEnabledOption : '[data-menu-enabled-option]', // Options when the menu is enabled
		disableMenuCheckbox : '[data-disable-menu-checkbox]', // Checkbox for disabling menus completely
		disableAutoCheckbox : '[data-menu-disable-auto-checkbox]', // Checkbox for disabling auto menu sync
		
		// Page Assignment for Post Types
		assignPostTypeCheckbox : '[data-nestedpages-assign-post-type]', // Checkbox for assigning a post type to a page
		assignPostTypeId : '[data-nested-pages-assign-post-type-id]', // Hidden field containing the assigned page id
		assignPostTypeTitle : '[data-nested-pages-assign-post-type-title]', // Hidden field containing the assigned page title
		assignPostTypeOption : '[data-assignment-page-id]', // Option within the listing to select page for post type assignment
		assignPostTypeRemove : '[data-nestedpages-page-pt-assignment-remove]', // Link to remove the assigned page for the post type,
		assignPostTypeSelection : '[data-nestedpages-page-pt-assignment-selection]', // The div displaying the selection

		enableMaximumNestingLevel : '[data-nested-pages-enable-maximum-nesting]', // Checkbox to enable maximum nesting level depth,
		maximumNestingLevel : '[data-nested-pages-maximum-nesting]', // Input for setting maximum nesting level depth

		// Sort Options for Post Types
		sortOptionCheckbox : '[data-nestedpages-sort-option-checkbox]', // Checkbox for enabling a sort option
		defaultSortOptions : '[data-nestedpages-sort-option-default]', // Default sort options (containing div)
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.toggleAllSettingsButtons();
			plugin.toogleAllFieldSettings('.custom-fields');
			plugin.toogleAllFieldSettings('.standard-fields');
			plugin.toggleMenuCheckboxes();
			plugin.toggleHideCheckbox();
			plugin.toggleAssignPostType();
			plugin.toggleAllDefaultSortOptions();
		});
		$(document).on('click', plugin.selectors.postTypeToggle, function(e){
			e.preventDefault();
			plugin.toggleRow($(this));
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
		$(document).on('change', plugin.selectors.assignPostTypeCheckbox, function(){
			plugin.toggleAssignPostType();
		});
		$(document).on('click', plugin.selectors.assignPostTypeOption, function(e){
			e.preventDefault();
			plugin.chooseAssignPostType($(this));
		});
		$(document).on('click', plugin.selectors.assignPostTypeRemove, function(e){
			e.preventDefault();
			plugin.removeAssignPostType($(this));
		});
		$(document).on('change', plugin.selectors.sortOptionCheckbox, function(){
			plugin.toggleDefaultSortOptions($(this));
		});
		$(document).on('change', plugin.selectors.enableMaximumNestingLevel, function(){
			plugin.toggleMaximumNesting($(this));
		});
	}

	/**
	* Toggle Individual Post Type Settings
	*/
	plugin.toggleRow = function(button)
	{
		$(button).parent('.head').siblings('.body').toggle();
		$(button).parents('.row-container').toggleClass('active');
	}

	/**
	* Show/Hide the settings toggle button for enabled/disabled post types
	*/
	plugin.toggleSettingsButton = function(checkbox)
	{
		var button = $(checkbox).parents('.head').find(plugin.selectors.postTypeToggle);
		if ( $(checkbox).is(':checked') ){
			$(button).show();
			$(button).parents('.head').siblings('.body').find('input[type="hidden"]').attr('disabled', false);
			return;
		}
		$(button).hide();
		$(button).parents('.head').siblings('.body').hide();
		$(button).parents('.row-container').removeClass('active');
		$(button).parents('.head').siblings('.body').find('input[type="checkbox"]').attr('checked', false);
		$(button).parents('.head').siblings('.body').find('input[type="hidden"]').attr('disabled', true);
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

	/**
	* Toggle the Assign Page to a Post Type Listing
	*/
	plugin.toggleAssignPostType = function()
	{
		var checkboxes = $(plugin.selectors.assignPostTypeCheckbox);
		$.each(checkboxes, function(){
			var checkbox = $(this);
			var listing = $(this).parents('.field').find('.nestedpages-assignment-display');
			if ( $(checkbox).is(':checked') ){
				$(listing).show();
			} else {
				$(listing).hide();
			}
		});
	}

	/**
	* Choose a page assignment
	*/
	plugin.chooseAssignPostType = function(element)
	{
		var pageId = $(element).attr('data-assignment-page-id');
		var pageTitle = $(element).attr('data-assignment-page-title');
		var container = $(element).parents('.field');
		var html = nestedpages.currently_assigned_to + ' ' + pageTitle + ' <a href="#" data-nestedpages-page-pt-assignment-remove>(' + nestedpages.remove + ')</a>';
		$(container).find(plugin.selectors.assignPostTypeId).val(pageId);
		$(container).find(plugin.selectors.assignPostTypeTitle).val(pageTitle);
		$(container).find(plugin.selectors.assignPostTypeSelection).html(html).show();
		$(container).find('[data-nestedpages-post-search-form]').hide();
	}

	/**
	* Remove a page assignment
	*/
	plugin.removeAssignPostType = function(element)
	{
		var container = $(element).parents('.field');
		$(container).find(plugin.selectors.assignPostTypeSelection).hide();
		$(container).find('[data-nestedpages-post-search-form]').show();
		$(container).find(plugin.selectors.assignPostTypeId).val('');
		$(container).find(plugin.selectors.assignPostTypeTitle).val('');
	}

	/**
	* Toggle all the default sort options
	*/
	plugin.toggleAllDefaultSortOptions = function()
	{
		var checkboxes = $(plugin.selectors.sortOptionCheckbox);
		$.each(checkboxes, function(){
			plugin.toggleDefaultSortOptions($(this));
		});
	}

	/**
	* Toggle the default sort options
	*/
	plugin.toggleDefaultSortOptions = function(checkbox)
	{
		var checked = ( $(checkbox).is(':checked') ) ? true : false;
		var options = $(checkbox).parent('label').next(plugin.selectors.defaultSortOptions);
		if ( $(options).length < 1 ) return;
		if ( checked ) {
			$(options).show();
			return;
		}
		$(options).hide();
	}

	/**
	* Toggle the Maximum Nesting Level Option
	*/
	plugin.toggleMaximumNesting = function(checkbox)
	{
		var checked = ( $(checkbox).is(':checked') ) ? true : false;
		var input = $(checkbox).parents('.field').find(plugin.selectors.maximumNestingLevel);
		if ( checked ){
			$(input).show();
			return;
		}
		$(input).hide();
	}

	return plugin.bindEvents();
}

new NestedPages.Settings;