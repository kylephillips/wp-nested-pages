var NestedPages = NestedPages || {};

/**
* Add new post(s) - Top level & child
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.NewPost = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.parent_id = 0; // Parent ID for the post(s) to add
	plugin.posts = ''; // The newly added posts
	plugin.form = ''; // The active form

	plugin.bindEvents = function()
	{
		$(document).on('open-modal', function(e, button, modal){
			var target = $(button).attr('data-nestedpages-modal-toggle');
			if ( typeof target !== 'undefined' && target == 'np-bulk-modal' ){
				plugin.openModal();
			}
		});
		$(document).on('submit', NestedPages.selectors.newPageForm, function(e){
			e.preventDefault();
		});
		$(document).on('click', NestedPages.selectors.newPageSubmitButton, function(e){
			e.preventDefault();
			plugin.submitForm($(this));
		});
		$(document).on('click', NestedPages.selectors.newPageTitle, function(e){
			e.preventDefault();
			plugin.addTitleField($(this));
		});
		$(document).on('click', NestedPages.selectors.newPageRemoveTitle, function(e){
			e.preventDefault();
			plugin.removeTitleField($(this));
		});
		$(document).on('click', NestedPages.selectors.addChildButton, function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('click', NestedPages.selectors.cancelNewChildButton, function(e){
			e.preventDefault();
			plugin.cancelNewPage();
		});
		$(document).on('click', '[' + NestedPages.selectors.newBeforeButton + ']', function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('click', '[' + NestedPages.selectors.newAfterButton + ']', function(e){
			e.preventDefault();
			plugin.openQuickEdit($(this));
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) {
				plugin.cancelNewPage();
				$(document).click(); // Close Dropdown
			}
		});
	}

	// Open the form modal
	plugin.openModal = function()
	{
		var newform = $(NestedPages.selectors.newPageFormContainer).clone().find(NestedPages.selectors.newPageForm);
		$(newform).addClass('in-modal');
		$(NestedPages.selectors.newPageModal).find('.modal-body').html(newform);
		$(NestedPages.selectors.newPageModal).find('h3').text(nestedpages.add_multiple);
		$(NestedPages.selectors.newPageModal).find('.page_parent_id').val(plugin.parent_id);
		$(newform).find('.np_title').first().focus();
		$(newform).find(NestedPages.selectors.newPageTitle).first().prop('tabindex', '2');
	}

	// Open the new child quick edit
	plugin.openQuickEdit = function(button)
	{
		var before = $(button).attr(NestedPages.selectors.newBeforeButton);
		before = ( typeof before === 'undefined' || before === '' ) ? false : before;

		var after = $(button).attr(NestedPages.selectors.newAfterButton);
		after = ( typeof after === 'undefined' || after === '' ) ? false : after;

		var parent_li = $(button).closest(NestedPages.selectors.row).parent('li');
		var newform = $(NestedPages.selectors.newPageFormContainer).clone();

		// Append the form to the list item
		if ( $(parent_li).children('ol').length > 0 ){
			var child_ol = $(parent_li).children('ol');
			$(newform).insertBefore(child_ol);
		} else {
			$(newform).appendTo(parent_li);
		}


		$(newform).siblings(NestedPages.selectors.row).hide();

		plugin.formatter.showQuickEdit();

		$(newform).find('.parent_name').html('<em>Parent:</em> ' + $(button).attr('data-parentname'));
		if ( !before && !after ) $(newform).find('.page_parent_id').val($(button).attr('data-id'));

		if ( before ) {
			$(newform).find('.page_before_id').val(before);
			$(newform).find('[data-new-post-relation-title]').text(nestedpages.insert_before + ': ' + $(button).attr('data-parentname'));
		}
		if ( after ) {
			$(newform).find('.page_after_id').val(after);
			$(newform).find('[data-new-post-relation-title]').text(nestedpages.insert_after + ': ' + $(button).attr('data-parentname'));
		}

		$(newform).show();
		$(newform).find('.np_title').focus();
		$(newform).find(NestedPages.selectors.newPageTitle).prop('tabindex', '2');
	}

	// Close the form modal
	plugin.cancelNewPage = function()
	{
		plugin.formatter.removeQuickEdit();
		$(NestedPages.selectors.newChildError).hide();
		$(NestedPages.selectors.newPageModal).find('.modal-body').empty();
		$(NestedPages.selectors.sortable).find('.new-child').remove();
		$(NestedPages.selectors.row).show();
	}

	// Add a page title field to the form
	plugin.addTitleField = function(button)
	{		
		var form = $(button).parents('form');
		var fieldcount = $(button).siblings('.new-page-titles').children('li').length + 1;
		var html = '<li><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg><div class="form-control new-child-row"><label>' + NestedPages.jsData.titleText + '</label><div><input type="text" name="post_title[]" class="np_title" placeholder="' + NestedPages.jsData.titleText + '" value="" tabindex="' + fieldcount + '" /><a href="#" class="button-secondary np-remove-child">-</a></div></div></li>';
		var container = $(button).siblings('.new-page-titles').append(html);
		$(form).find('.np_title').last().focus();
		$(form).find(NestedPages.selectors.newPageTitle).prop('tabindex', fieldcount++);
		$('.new-page-titles').sortable({
			items : 'li',
			handle: '.handle',
		});
		plugin.toggleAddEditButton(form);
	}

	// Remove a page title field
	plugin.removeTitleField = function(button)
	{
		var form = $(button).parents('form');
		$(button).parents('.new-child-row').parent('li').remove();
		plugin.toggleAddEditButton(form);
	}

	// Submit the New Page Form
	plugin.submitForm = function(button)
	{
		plugin.toggleLoading(true);
		plugin.form = $(button).parents('form');

		var addedit = ( $(button).hasClass('add-edit') ) ? true : false;
		var action = NestedPages.formActions.newPage;
		if ( $(plugin.form).find('.page_before_id').val() !== '' ) action = NestedPages.formActions.newBeforeAfter;
		if ( $(plugin.form).find('.page_after_id').val() !== '' ) action = NestedPages.formActions.newBeforeAfter;
		
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).serialize() + '&action=' + action + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				if (data.status === 'error'){
					plugin.toggleLoading(false);
					$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
					return;
				}
				if ( addedit === true ){ // Redirect to Edit Screen
					var link = data.new_pages[0].edit_link;
					link = link.replace(/&amp;/g, '&');
					window.location.replace(link);
					return;
				}
				plugin.toggleLoading(false);
				plugin.posts = data.new_pages;
				plugin.addPosts();
			},
			error: function(data){
				console.log(data);
				plugin.toggleLoading(false);
				$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text('The form could not be saved at this time.').show();
			}
		});
	}

	// Add the new posts
	plugin.addPosts = function()
	{
		// Before/After ID if applicable
		var before = $(plugin.form).find('.page_before_id').val();
		before = ( before !== '' ) ? before : false;
		var after = $(plugin.form).find('.page_after_id').val();
		after = ( after !== '' ) ? after : false;

		var parent_li = $(plugin.form).parent('.new-child').parent('.page-row');
		
		// If parent li doesn't have a child ol, add one
		if ( $(parent_li).children('ol').length === 0 && !before && !after ){
			$(parent_li).append('<ol class="nplist"></ol>');
		}

		if ( $(plugin.form).hasClass('in-modal') ){
			var appendto = $('.nplist.sortable li.page-row:first');
		} else {
			var appendto = $(parent_li).children('ol');
		}

		for (i = 0; i < plugin.posts.length; i++){
			plugin.appendRows(appendto, plugin.posts[i], before, after);
		}

		// Show the child page list and reset submenu toggles
		if ( !before && !after ){
			$(appendto).show();
		}

		plugin.formatter.updateSubMenuToggle();
		plugin.formatter.setNestedMargins();
		plugin.cancelNewPage();
		$(document).trigger('close-modal-manual');
	}

	// Append new post rows to the nested view
	plugin.appendRows = function(appendto, post, before, after)
	{
		var html = '<li id="menuItem_' + post.id + '" class="page-row';
		if ( post.status === 'publish' ) html += ' published';
		html += '">';

		if ( NestedPages.jsData.hierarchical ){
			html += '<div class="row">';
			html += '<div class="child-toggle"></div>';
		} else {
			html += '<div class="row non-hierarchical">';
			html += '<div class="non-hierarchical-spacer"></div>';
		}

		html += '<div class="row-inner">';
		// Submenu
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-sub-menu"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 15l-6 6-1.42-1.42L15.17 16H4V4h2v10h9.17l-3.59-3.58L13 9l6 6z" class="arrow" /></svg>';
		// Handle
		html += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>';
		html += '<a href="' + post.edit_link + '" class="page-link page-title">';
		html += '<span class="title">' + post.title + '</span>';
		
		// Status
		if ( post.status !== 'Publish' ){
			html += '<span class="status">(' + post.status + ')</span>';
		} else {
			html += '<span class="status"></span>';
		}

		// Nav Status
		html += '<span class="nav-status">';
		if ( post.np_nav_status === 'hide' ){
			html += ' ' + nestedpages.hidden;
		}
		html += '</span>';

		html += '<span class="edit-indicator">Edit</span>';
		html += '</a>';

		// Non-Hierarchical Data
		if ( !NestedPages.jsData.hierarchical ){
			html += '<div class="np-post-columns">';
			html += '<ul class="np-post-info">';
			html += '<li><span class="np-author-display">' + post.author_formatted + '</span></li>';
			html += '<li>' + post.date_formatted + '</li>';
			html += '</ul>';
			html += '</div>';
		}

		// Yoast
		if ( $('.nplist').first().hasClass('has-yoast') ) {
			html += '<span class="np-seo-indicator na"></span>';
		}

		// Action Buttons
		html += '<div class="action-buttons">';
		html += '<div class="nestedpages-dropdown" data-dropdown><a href="#" class="np-btn has-icon toggle" data-dropdown-toggle><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg></a><ul class="nestedpages-dropdown-content" data-dropdown-content>';
		// Add Link
		html += '<li><a href="#" class="open-redirect-modal" data-parentid="' + post.id + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>' + nestedpages.add_link + '</a></li>';
		// Add Child
		html += '<li><a href="#" class="add-new-child" data-id="' + post.id + '" data-parentname="' + post.title + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zM3 8v8l4-4-4-4zm8 9h10v-2H11v2zM3 3v2h18V3H3zm8 6h10V7H11v2zm0 4h10v-2H11v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>' + nestedpages.add_child_short + '</a></li>';
		html += '</ul></div>';
		
		// Quick Edit (data attrs)
		html += '<a href="#" class="np-btn np-quick-edit" data-id="' + post.id + '" data-template="' + post.page_template + '" data-title="' + post.title + '" data-slug="' + post.slug + '" data-commentstatus="closed" data-status="' + post.status.toLowerCase() + '" data-np-status="show"	data-navstatus="show" data-author="' + post.author + '" data-template="' + post.template + '" data-month="' + post.month + '" data-day="' + post.day + '" data-year="' + post.year + '" data-hour="' + post.hour + '" data-minute="' + post.minute + '" data-datepicker="' + post.datepicker + '" data-time="' + post.time + '" data-formattedtime="' + post.formattedtime + '" data-ampm="' + post.ampm + '">' + nestedpages.quick_edit + '</a>';

		html += '<a href="' + post.view_link + '" class="np-btn" target="_blank">' + nestedpages.view + '</a>';

		// Trash
		html += '<a href="' + post.delete_link + '" class="np-btn np-btn-trash"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-remove"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg></a>';
		html += '</div><!-- .action-buttons -->';

		html += '</div><!-- .row-inner --></div><!-- .row -->';
		html += '</li>';

		if ( before ){
			var row = plugin.findRowById(before);
			$(html).insertBefore(row);
			return;
		}
		if ( after ){
			var row = plugin.findRowById(after);
			$(html).insertAfter(row);
			return;
		}

		$(appendto).append(html);
	}

	// Find the row for inserting before/after
	plugin.findRowById = function(id)
	{
		var row = $(NestedPages.selectors.rows + '#menuItem_' + id);
		return row;
	}

	// Toggle the "Add & Edit" & "Add" buttons depending on row count
	plugin.toggleAddEditButton = function(form)
	{
		var titleCount = $(form).find('.np_title').length;
		if ( titleCount < 1 ){
			$(NestedPages.selectors.newPageSubmitButton).hide();
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton).show();
		if ( titleCount > 1 ){
			$(NestedPages.selectors.newPageSubmitButton + '.add-edit').hide()
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton + '.add-edit').show()
	}

	// Toggle the form loading state
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.quickEditErrorDiv).hide();
			$(NestedPages.selectors.newPageSubmitButton).attr('disabled', 'disabled');
			$(NestedPages.selectors.quickEditLoadingIndicator).show();
			return;
		}
		$(NestedPages.selectors.newPageSubmitButton).attr('disabled', false);
		$(NestedPages.selectors.quickEditLoadingIndicator).hide();
	}

	return plugin.bindEvents();
}