var NestedPages = NestedPages || {};

/**
* Add a new Link (top level and child)
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
* @todo - remove in 1.4.1
*/
NestedPages.NewLink = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.postParent = 0; // Parent Post ID
	plugin.post = ''; // New Post Data
	plugin.row = ''; // Newly Appended Row

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}


	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.openLinkModal, function(e){
			e.preventDefault();
			plugin.postParent = $(this).attr('data-parentid');
			plugin.openModal();
		});
		$(document).on('click', NestedPages.selectors.saveLink, function(e){
			e.preventDefault();
			plugin.saveLink();
		});
	}


	// Open the Modal and Clear/Populate hidden fields
	plugin.openModal = function()
	{
		$(NestedPages.selectors.linkErrorDiv).hide();
		$(NestedPages.selectors.linkModal).find('input').val('');
		$(NestedPages.selectors.linkModal).find('.parent_id').val(plugin.postParent);
		$(NestedPages.selectors.linkModal).modal('show');
	}


	// Toggle the loading state in the link modal
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.linkErrorDiv).hide();
			$(NestedPages.selectors.linkLoadingIndicator).show();
			$(NestedPages.selectors.saveLink).attr('disabled', 'disabled');
			return;
		}
		$(NestedPages.selectors.linkLoadingIndicator).hide();
		$(NestedPages.selectors.saveLink).attr('disabled', false);
	}


	// Save the link
	plugin.saveLink = function()
	{
		plugin.toggleLoading(true);
		var data = $(NestedPages.selectors.linkForm).serialize();

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'POST',
			datatype: 'json',
			data: data + '&action=' + NestedPages.formActions.newLink + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				plugin.toggleLoading(false);
				if (data.status === 'error'){
					$(NestedPages.selectors.linkErrorDiv).text(data.message).show();
					return;
				}
				plugin.post = data.post_data;
				plugin.createRow();
			}
		});
	}


	// Create the nested pages row for the new link
	plugin.createRow = function()
	{
		var html = '<li id="menuItem_' + plugin.post.id + '" class="page-row';
		if ( plugin.post._status === 'publish' ) html += ' published';
		html += '">'

		html += '<div class="row"><div class="child-toggle"><div class="child-toggle-spacer"></div></div><div class="row-inner"><i class="np-icon-sub-menu"></i><i class="handle np-icon-menu"></i><a href="' + plugin.post.np_link_content + '" class="page-link page-title" target="_blank"><span class="title">' + plugin.post.np_link_title + ' <i class="np-icon-link"></i></span>';

		// Post Status
		html += '<span class="status">';
		if ( plugin.post._status !== 'publish' ) html += plugin.post._status;
		html += '</span>';

		// Nested Pages Status
		if ( plugin.post.np_status === "hide" )	html += '<i class="np-icon-eye-blocked"></i>';

		// Nav Menu Status
		html += '<span class="nav-status">';
		if ( plugin.post.nav_status === "hide" ) html += '(' + NestedPages.jsData.hiddenText + ')';
		html += '</span>';

		// Quick Edit Button
		html += '</a><a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a><div class="action-buttons"><a href="#" class="np-btn np-quick-edit-redirect" ';
		html +=	'data-id="' + plugin.post.id + '"'; 
		html += 'data-parentid="' + plugin.post.parent_id + '"';
		html += 'data-title="' + plugin.post.np_link_title + '" ';
		html += 'data-url="' + plugin.post.np_link_content + '" ';
		html += 'data-status="' + plugin.post._status + '" ';
		html += 'data-np-status="' + plugin.post.np_status + '" ';
		html += 'data-navstatus="' + plugin.post.nav_status + '" ';
		html += 'data-linktarget="' + plugin.post.link_target + '">'
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


	return plugin.init();

}