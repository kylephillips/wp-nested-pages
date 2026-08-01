var NestedPages = NestedPages || {};

/**
* Quick Edit functionality for links
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.QuickEditLink = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;
	plugin.button = ''; // The Active Quick Edit Button
	plugin.postData = ''; // Data for Post being edited (before update)
	plugin.newPostData = ''; // Data after update
	plugin.form = ''; // The newly created Quick Edit Form

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.quickEditButtonLink, function(e){
			e.preventDefault();
			plugin.formatter.removeQuickEdit();
			plugin.button = $(this);
			plugin.openQuickEdit();
		});
		$(document).on('click', NestedPages.selectors.quickEditLinkSaveButton, function(e){
			e.preventDefault();
			plugin.submitForm();
		});
		$(document).on('keydown', function(e){
			if ( e.keyCode === 27 ) plugin.formatter.removeQuickEdit();
		});
	}


	// Open the Quick Edit Form
	plugin.openQuickEdit = function()
	{
		plugin.setData();
		plugin.createForm();
		plugin.populateForm();
	}


	// Set the Quick Edit Data
	plugin.setData = function()
	{
		plugin.postData = {
			id : $(plugin.button).attr('data-id'),
			url : $(plugin.button).attr('data-url'),
			title : $(plugin.button).attr('data-title'),
			status : $(plugin.button).attr('data-status'),
			navstatus : $(plugin.button).attr('data-navstatus'),
			npstatus : $(plugin.button).attr('data-np-status'),
			linktarget : $(plugin.button).attr('data-linktarget'),
			parentid : $(plugin.button).attr('data-parentid'),
			navtitleattr : $(plugin.button).attr('data-navtitleattr'),
			navcss : $(plugin.button).attr('data-navcss'),
			navtype : $(plugin.button).attr('data-nav-type'),
			navobject : $(plugin.button).attr('data-nav-object'),
			navobjectid : $(plugin.button).attr('data-nav-object-id'),
			navoriginallink : $(plugin.button).attr('data-nav-original-link'),
			navoriginaltitle : $(plugin.button).attr('data-nav-original-title')
		};
	}


	// Create the quick edit form
	plugin.createForm = function()
	{
		var parent_li = $(plugin.button).closest(NestedPages.selectors.row).parent('li');
		plugin.form = $(NestedPages.selectors.quickEditLinkForm).clone();
		
		// Append the form to the list item
		if ( $(parent_li).children('ol').length > 0 ){
			var child_ol = $(parent_li).children('ol');
			$(plugin.form).insertBefore(child_ol);
		} else {
			$(plugin.form).appendTo(parent_li);
		}

		var row = $(plugin.form).siblings(NestedPages.selectors.row).hide();
	}


	// Populate the Quick Edit form with the post data
	plugin.populateForm = function()
	{
		$(plugin.form).find('.np_id').val(plugin.postData.id);
		$(plugin.form).find('.np_title').val(plugin.postData.title);
		$(plugin.form).find('.np_author select').val(plugin.postData.author);
		$(plugin.form).find('.np_status').val(plugin.postData.status);
		$(plugin.form).find('.np_content').val(plugin.postData.url);
		$(plugin.form).find('.np_parent_id').val(plugin.postData.parentid);
		$(plugin.form).find('.np_title_attribute').val(plugin.postData.navtitleattr);
		$(plugin.form).find('.np_nav_css_classes').val(plugin.postData.navcss);

		if ( plugin.postData.npstatus === 'hide' ){
			$(plugin.form).find('.np_status').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.np_status').removeAttr('checked');
		}
		
		if ( plugin.postData.navstatus === 'hide' ) {
			$(plugin.form).find('.np_nav_status').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.np_nav_status').removeAttr('checked');
		}

		if ( plugin.postData.linktarget === "_blank" ) {
			$(plugin.form).find('.link_target').prop('checked', 'checked');
		} else {
			$(plugin.form).find('.link_target').removeAttr('checked');
		}

		// Relationship Links
		if ( plugin.postData.navobject !== 'custom' && plugin.postData.navobject !== '' ){
			var html = '<div class="form-control original-link">Original: <a href="' + plugin.postData.navoriginallink + '" target="_blank">' + plugin.postData.navoriginaltitle + '</a></div>';
			$(plugin.form).find('[data-url-field]').remove();
			$(html).insertAfter($(plugin.form).find('h3'));
			$(plugin.form).find('[data-np-menu-object-input]').val(plugin.postData.navobject);
			$(plugin.form).find('[data-np-menu-objectid-input]').val(plugin.postData.navobjectid);
			$(plugin.form).find('[data-np-menu-type-input]').val(plugin.postData.navtype);
			$(plugin.form).find('h3').text('Link: ' + plugin.postData.navoriginaltitle);
		} else {
			$(plugin.form).find('h3').text('Link');
			$(plugin.form).find('[data-np-menu-object-input]').val('custom');
			$(plugin.form).find('[data-np-menu-type-input]').val('custom');
		}

		plugin.formatter.showQuickEdit();
		$(plugin.form).show();
	}


	// Submit the form
	plugin.submitForm = function()
	{
		plugin.toggleLoading(true);

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: $(plugin.form).find('form').serialize() + '&action=' + NestedPages.formActions.quickEditLink + '&nonce=' + NestedPages.jsData.nonce + '&syncmenu=' + NestedPages.jsData.syncmenu + '&post_type=' + NestedPages.jsData.posttype,
			success: function(data){
				if (data.status === 'error'){
					plugin.toggleLoading(false);
					$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
				} else {
					plugin.toggleLoading(false);
					plugin.newPostData = data.post_data;
					plugin.updateRow();					
				}
			},
			error: function(data){
				plugin.toggleLoading(false);
				$(plugin.form).find(NestedPages.selectors.quickEditErrorDiv).text('The form could not be saved at this time.').show();
			}
		});
	}


	// Update the row after successfully saving quick edit data
	plugin.updateRow = function()
	{
		var row = $(plugin.form).siblings('.row');
		$(row).find('.title').html(plugin.newPostData.post_title + ' <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="icon" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>');
		
		var status = $(row).find('.status');
		if ( (plugin.newPostData._status !== 'publish') && (plugin.newPostData._status !== 'future') ){
			$(status).text('(' + plugin.newPostData._status + ')');
		} else {
			$(status).text('');
		}

		// Hide / Show in Nav
		var nav_status = $(row).find('.nav-status');
		if ( (plugin.newPostData.nav_status == 'hide') ){
			$(nav_status).text('(' + NestedPages.jsData.hiddenText + ')');
		} else {
			$(nav_status).text('');
		}

		// Hide / Show in Nested Pages
		var li = $(row).parent('li');
		if ( (plugin.newPostData.np_status == 'hide') ){
			$(li).addClass('np-hide');
			$(row).find('.status').after('<svg class="row-status-icon status-np-hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path class="icon" d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>');
		} else {
			$(li).removeClass('np-hide');
			$(row).find('.status-np-hidden').remove();
		}

		var button = $(row).find(NestedPages.selectors.quickEditButtonLink);

		$(button).attr('data-id', plugin.newPostData.post_id);
		$(button).attr('data-title', plugin.newPostData.post_title);
		$(button).attr('data-url', plugin.newPostData.post_content);
		$(button).attr('data-status', plugin.newPostData._status);
		$(button).attr('data-navstatus', plugin.newPostData.nav_status);
		$(button).attr('data-np-status', plugin.newPostData.np_status);
		$(button).attr('data-linkTarget', plugin.newPostData.linkTarget);
		$(button).attr('data-navtitleattr', plugin.newPostData.titleAttribute);
		$(button).attr('data-navcss', plugin.newPostData.cssClasses);

		plugin.formatter.removeQuickEdit();
		plugin.formatter.flashRow(row);
	}


	// Toggle loading state in form
	plugin.toggleLoading = function(loading)
	{
		$('.row').removeClass('np-updated').removeClass('np-updated-show');
		if ( loading ){
			$(NestedPages.selectors.quickEditErrorDiv).hide();
			$(NestedPages.selectors.quickEditLinkSaveButton).attr('disabled', 'disabled');
			$(NestedPages.selectors.quickEditLoadingIndicator).show();
			return;
		}
		$(NestedPages.selectors.quickEditLinkSaveButton).attr('disabled', false);
		$(NestedPages.selectors.quickEditLoadingIndicator).hide();
	}

	return plugin.bindEvents();

}