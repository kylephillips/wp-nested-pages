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
	plugin.post = ''; // The new post created

	plugin.formatter = new NestedPages.Formatter;

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
		$(document).on('click', NestedPages.selectors.openLinkModal, function(e){
			e.preventDefault();
			plugin.postParent = $(this).attr('data-parentid');
			$(plugin.selectors.form).find('.parent_id').val($(this).attr('data-parentid'));
			plugin.openModal();
		});
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
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) $('#np-link-modal').modal('hide');
		});
	}

	// Open the Modal and Clear/Populate hidden fields
	plugin.openModal = function()
	{
		$(NestedPages.selectors.linkErrorDiv).hide();
		$(NestedPages.selectors.linkModal).find('input').val('');
		$(NestedPages.selectors.linkModal).find('.parent_id').val(plugin.postParent);
		plugin.clearForm();
		$(plugin.selectors.accordion).find('ul').hide();
		$(plugin.selectors.typeSelect).removeClass('active');
		$(NestedPages.selectors.linkModal).modal('show');
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
		if ( $(plugin.typeButton).hasClass('np-custom-link') ){
			$(plugin.selectors.accordionItem).removeClass('active');
			$(plugin.selectors.accordion).find('ul').slideUp('fast');
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
		$(plugin.selectors.form).find('input').not('.parent_id').val('');
		$(plugin.selectors.form).find(plugin.fields.linkTarget).val('_blank');
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
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data: $(plugin.selectors.form).serialize() + '&action=' + NestedPages.formActions.newMenuItem + '&nonce=' + NestedPages.jsData.nonce + '&post_type=' + NestedPages.jsData.posttype + '&syncmenu=' + NestedPages.jsData.syncmenu,
			success : function(data){
				plugin.toggleLoading(false);
				if ( data.status === 'error' ){
					$(plugin.selectors.form).find(plugin.selectors.errorDiv).text(data.message).show();
					return;
				}
				plugin.post = data.post_data;
				plugin.createRow();
			},
			error : function(data){
				console.log(data);
			}
		});
	}

	// Create the nested pages row for the new link
	plugin.createRow = function()
	{
		var html = '<li id="menuItem_' + plugin.post.id + '" class="page-row published';
		html += '">'

		html += '<div class="row"><div class="child-toggle"><div class="child-toggle-spacer"></div></div><div class="row-inner"><i class="np-icon-sub-menu"></i><i class="handle np-icon-menu"></i><a href="' + plugin.post.np_link_content + '" class="page-link page-title" target="_blank"><span class="title">' + plugin.post.menuTitle + ' <i class="np-icon-link"></i></span>';

		// Quick Edit Button
		html += '</a><a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a><div class="action-buttons"><a href="#" class="np-btn np-quick-edit-redirect" ';
		html +=	'data-id="' + plugin.post.id + '"'; 
		html += 'data-parentid="' + plugin.post.parent_id + '"';
		html += 'data-title="' + plugin.post.menuTitle + '" ';
		html += 'data-url="' + plugin.post.url + '" ';
		html += 'data-status="publish" ';
		html += 'data-np-status="show" ';
		html += 'data-navstatus="show" ';
		html += 'data-navcss="' + plugin.post.cssClasses + '" ';
		html += 'data-navtitleattr="' + plugin.post.titleAttribute + '" ';
		html += 'data-nav-type="' + plugin.post.menuType + '" ';
		html += 'data-nav-object="' + plugin.post.objectType + '" ';
		html += 'data-nav-object-id="' + plugin.post.objectId + '" ';
		html += 'data-nav-original-link="' + plugin.post.original_link + '" ';
		html += 'data-nav-original-title="' + plugin.post.original_title + '" ';
		html += 'data-linktarget="' + plugin.post.link_target + '">';
		html += NestedPages.jsData.quickEditText;
		html += '</a>';

		// Delete Link
		html += '<a href="' + plugin.post.delete_link + '" class="np-btn np-btn-trash"><i class="np-icon-remove"></i></a>';

		html += '</div></div></div></li>';

		if ( plugin.post.parent_id === "0" ){
			$('.nplist:first li:first').after(html);
		} else {
			plugin.appendChildRow(html);
		}

		$(NestedPages.selectors.linkModal).modal('hide');

		plugin.row = $('#menuItem_' + plugin.post.id).find('.row');
		plugin.formatter.flashRow(plugin.row);
	}

	// Append a new child link to the appropriate menu
	plugin.appendChildRow = function(html)
	{
		var parent_row = $('#menuItem_' + plugin.post.parent_id);
		if ( $(parent_row).children('ol').length === 0 ){
			html = '<ol class="sortable nplist" style="display:block;">' + html + '</ol>';
			$(parent_row).append(html);
		} else {
			$(parent_row).find('ol:first').prepend(html);
		}
		plugin.formatter.updateSubMenuToggle();
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