var NestedPages = NestedPages || {};

/**
* Add new page(s) - Top level & child
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.NewPage = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.parent_id = 0; // Parent ID for the post(s) to add
	plugin.posts = ''; // The newly added posts
	plugin.form = ''; // The active form


	plugin.init = function()
	{
		plugin.bindEvents();
	}


	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.openPageModal, function(e){
			e.preventDefault();
			plugin.openModal();
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
		$(NestedPages.selectors.newPageModal).on('hide.bs.modal', function(){
			plugin.cancelNewPage();
		});
		$(NestedPages.selectors.newPageModal).on('shown.bs.modal', function(){
			plugin.modalOpened($(this));
		});
		$(document).on('click', NestedPages.selectors.cancelNewChildButton, function(e){
			e.preventDefault();
			plugin.cancelNewPage();
			$(NestedPages.selectors.newPageModal).modal('hide');
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
		$(NestedPages.selectors.newPageModal).modal('show');
	}

	// Modal has opened, set the attributes
	plugin.modalOpened = function(modal)
	{
		$(modal).find('.np_title').focus();
		$(modal).find(NestedPages.selectors.newPageTitle).prop('tabindex', '2');
	}


	// Open the new child quick edit
	plugin.openQuickEdit = function(button)
	{
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
		$(newform).find('.page_parent_id').val($(button).attr('data-id'));
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
		var html = '<li><i class="handle np-icon-menu"></i><div class="form-control new-child-row"><label>' + NestedPages.jsData.titleText + '</label><div><input type="text" name="post_title[]" class="np_title" placeholder="' + NestedPages.jsData.titleText + '" value="" tabindex="' + fieldcount + '" /><a href="#" class="button-secondary np-remove-child">-</a></div></div></li>';
		var container = $(button).siblings('.new-page-titles').append(html);
		$(form).find('.np_title').last().focus();
		$(form).find(NestedPages.selectors.newPageTitle).prop('tabindex', fieldcount++);
		$('.new-page-titles').sortable({
			items : 'li',
			handle: '.handle',
		});
	}


	// Remove a page title field
	plugin.removeTitleField = function(button)
	{
		$(button).parents('.new-child-row').parent('li').remove();
	}


	// Submit the New Page Form
	plugin.submitForm = function(button)
	{
		plugin.toggleLoading(true);
		plugin.form = $(button).parents('form');

		var addedit = ( $(button).hasClass('add-edit') ) ? true : false;

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).serialize() + '&action=' + NestedPages.formActions.newPage + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
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
		var parent_li = $(plugin.form).parent('.new-child').parent('.page-row');
		
		// If parent li doesn't have a child ol, add one
		if ( $(parent_li).children('ol').length === 0 ){
			$(parent_li).append('<ol class="nplist"></ol>');
		}

		if ( $(plugin.form).hasClass('in-modal') ){
			var appendto = $('.nplist.sortable li.page-row:first');
		} else {
			var appendto = $(parent_li).children('ol');
		}

		for (i = 0; i < plugin.posts.length; i++){
			plugin.appendRows(appendto, plugin.posts[i]);
		}

		// Show the child page list and reset submenu toggles
		$(appendto).show();
		plugin.formatter.updateSubMenuToggle();
		plugin.cancelNewPage();
		$(NestedPages.selectors.newPageModal).modal('hide');
	}


	// Append new post rows to the nested view
	plugin.appendRows = function(appendto, post)
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
		html += '<i class="np-icon-sub-menu"></i><i class="handle np-icon-menu"></i>';
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

		html += '<span class="edit-indicator"><i class="np-icon-pencil"></i>Edit</span>';
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
		html += '<a href="#" class="np-btn open-redirect-modal" data-parentid="' + post.id + '"><i class="np-icon-link"></i></a>';
		html += '<a href="#" class="np-btn add-new-child" data-id="' + post.id + '" data-parentname="' + post.title + '">' + nestedpages.add_child_short + '</a>';
		
		// Quick Edit (data attrs)
		html += '<a href="#" class="np-btn np-quick-edit" data-id="' + post.id + '" data-template="' + post.page_template + '" data-title="' + post.title + '" data-slug="' + post.slug + '" data-commentstatus="closed" data-status="' + post.status.toLowerCase() + '" data-np-status="show"	data-navstatus="show" data-author="' + post.author + '" data-template="' + post.template + '" data-month="' + post.month + '" data-day="' + post.day + '" data-year="' + post.year + '" data-hour="' + post.hour + '" data-minute="' + post.minute + '" data-datepicker="' + post.datepicker + '" data-time="' + post.time + '" data-formattedtime="' + post.formattedtime + '" data-ampm="' + post.ampm + '">' + nestedpages.quick_edit + '</a>';

		html += '<a href="' + post.view_link + '" class="np-btn" target="_blank">' + nestedpages.view + '</a>';
		html += '<a href="' + post.delete_link + '" class="np-btn np-btn-trash"><i class="np-icon-remove"></i></a>';
		html += '</div><!-- .action-buttons -->';

		html += '</div><!-- .row-inner --></div><!-- .row -->';
		html += '</li>';

		$(appendto).append(html);
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



	return plugin.init();
}