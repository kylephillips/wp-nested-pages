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

	plugin.typeButton = ''; // The Link Type selected button

	plugin.selectors = {
		form : '[data-np-menu-item-form]', // The form element
		typeSelect : '[data-np-menu-selection]', // Link in left column to choose type of link
		accordion : '[data-np-menu-accordion]', // Accordion of objects
		accordionItem : '[data-np-menu-accordion-item]', // Single item in the accordion
		formPlaceholder : '.np-menu-link-object-placeholder', // Placeholder element
		formDetails : '.np-menu-link-details', // Right pane form details
		searchResults : '[data-np-search-result]', // Appended search result rows
		defaultResults : '[data-default-result]', // Default results,
		originalLink : '[data-np-original-link]', // Original Link Preview
		saveButton : '[data-np-save-link]', // The Form Submit Button
		urlInputCont : '[data-np-menu-url-cont]', // Container for URL input (only for custom links)
		errorDiv : '[data-np-error]', // The error notification
	}

	plugin.fields = {
		object : '[data-np-menu-object-input]', // The object (ex: post/category/custom)
		objectid : '[data-np-menu-objectid-input]', // ex: term id, post id
		itemType : '[data-np-menu-type-input]', // ex: post_type, taxonomy
		url : '[data-np-menu-url]', // custom url
		navigationLabel : '[data-np-menu-navigation-label]',
		titleAttribute : '[data-np-menu-title-attr]',
		cssClasses : '[data-np-menu-css-classes]',
		npStatus : '[data-np-menu-np-status]',
		linkTarget : '[data-np-menu-link-target]',
		menuTitle : '[data-np-menu-title]'
	}

	plugin.search = new NestedPages.MenuSearch;

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
		$(document).on('click', plugin.selectors.typeSelect, function(e){
			e.preventDefault();
			plugin.typeButton = $(this);
			plugin.setLinkType();
		});
		$(document).on('keyup', plugin.fields.navigationLabel, function(){
			plugin.updateTitle();
		});
		$(document).on('click', plugin.selectors.saveButton, function(e){
			e.preventDefault();
			plugin.submitForm();
		});
	}

	// Accordion Menu
	plugin.accordion = function(button)
	{
		plugin.clearForm();
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

	// Set the link type
	plugin.setLinkType = function()
	{
		if ( $(plugin.typeButton).hasClass('active') ){
			plugin.clearForm();
			return;
		}
		$(plugin.selectors.formPlaceholder).hide();
		plugin.populateForm();
	}

	// Populate the form
	plugin.populateForm = function()
	{
		$(plugin.selectors.saveButton).show();
		$(plugin.selectors.typeSelect).removeClass('active');
		$(plugin.typeButton).addClass('active');
		$(plugin.fields.menuTitle).text($(plugin.typeButton).text()).val($(plugin.typeButton).text());
		$(plugin.selectors.form).find('h3').find('em').text($(plugin.typeButton).attr('data-np-object-name'));
		if ( $(plugin.typeButton).attr('data-np-permalink') !== "" ){
			$(plugin.selectors.form).find(plugin.selectors.urlInputCont).hide();
			$(plugin.selectors.form).find(plugin.selectors.originalLink).html('<a href="' + $(plugin.typeButton).attr('data-np-permalink') + '">' + $(plugin.typeButton).text() + '</a>');
			$(plugin.selectors.form).find(plugin.selectors.originalLink).parent('.original-link').show();
		} else {
			$(plugin.selectors.form).find(plugin.selectors.urlInputCont).show();
			$(plugin.selectors.form).find(plugin.selectors.originalLink).parent('.original-link').hide();
		}
		$(plugin.fields.object).val($(plugin.typeButton).attr('data-np-menu-object'));
		$(plugin.fields.objectid).val($(plugin.typeButton).attr('data-np-menu-objectid'));
		$(plugin.fields.itemType).val($(plugin.typeButton).attr('data-np-menu-type'));
		$(plugin.selectors.formDetails).show();
	}

	// Clear the form
	plugin.clearForm = function()
	{
		$(plugin.selectors.form).find(plugin.selectors.errorDiv).hide();
		$(plugin.selectors.saveButton).hide();
		$(plugin.selectors.formDetails).hide();
		$(plugin.selectors.formPlaceholder).show();
		$(plugin.selectors.form).find('input').val('');
		$(plugin.selectors.form).find('input[type="checkbox"]').attr('checked', false);
		$(plugin.selectors.typeSelect).removeClass('active');
		plugin.search.toggleLoading(false);
		$(plugin.selectors.searchResults).remove();
		$(plugin.selectors.defaultResults).show();
	}

	// Update the title text
	plugin.updateTitle = function()
	{
		var value = $(plugin.fields.navigationLabel).val();
		var title = $(plugin.selectors.form).find('h3').find('span');
		if ( value !== "" ){
			$(plugin.fields.menuTitle).val(value);
			$(title).text(value);
		} else {
			$(plugin.fields.menuTitle).val($(plugin.typeButton).text());
			$(title).text($(plugin.typeButton).text());
		}
	}

	// Submit the Form
	plugin.submitForm = function()
	{
		plugin.toggleLoading(true);
		var syncmenu = ( $(NestedPages.selectors.syncCheckbox).is(':checked') ) ? 'sync' : 'nosync';
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data: $(plugin.selectors.form).serialize() + '&action=' + NestedPages.formActions.newMenuItem + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + syncmenu,
			success : function(data){
				console.log(data);
				plugin.toggleLoading(false);
				if ( data.status === 'error' ){
					$(plugin.selectors.form).find(plugin.selectors.errorDiv).text(data.message).show();
					return;
				}
				// console.log(data);
			},
			error : function(data){
				console.log(data);
			}
		});
	}

	// Toggle Loading
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(plugin.selectors.form).find(plugin.selectors.errorDiv).hide();
			$(plugin.selectors.form).find(NestedPages.selectors.quickEditLoadingIndicator).show();
			$(plugin.selectors.saveButton).attr('disabled', 'disabled');
			return;
		}
		$(plugin.selectors.form).find(NestedPages.selectors.quickEditLoadingIndicator).hide();
		$(plugin.selectors.saveButton).attr('disabled', false);
	}

	return plugin.init();
}