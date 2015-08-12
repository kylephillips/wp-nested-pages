/**
* Empty Trash Functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
jQuery(document).ready(function(){
	new NestedPagesTrash;
});

var NestedPagesTrash = function()
{
	var plugin = this;
	var $ = jQuery;

	// DOM Selectors
	plugin.trashButton = '.np-empty-trash'; // Trash Link
	plugin.confirmButton = '.np-trash-confirm'; // Confirm button in modal
	plugin.warningModal = '#np-trash-modal'; // Modal with empty confirmation
	plugin.errorAlert = '#np-error'; // Alert Error Notification
	plugin.loadingIndicator = '#nested-loading'; // Loading Indication
	plugin.trashLinks = '.np-trash-links';
	plugin.postType = $('#np-trash-posttype').val();

	// JS Data
	plugin.nonce = nestedpages.np_nonce;
	plugin.formAction = 'npEmptyTrash';


	// Initialization
	plugin.init = function(){
		plugin.bindEvents();
	}

	// Bind Events
	plugin.bindEvents = function(){
		$(document).on('click', plugin.trashButton, function(e){
			e.preventDefault();
			$(plugin.warningModal).modal('show');
		});
		$(document).on('click', plugin.confirmButton, function(e){
			e.preventDefault();
			plugin.confirmEmpty();
		});
	}

	// Confirm Trash Empty
	plugin.confirmEmpty = function(){
		plugin.loading(true);
		$(plugin.warningModal).hide();
		$(plugin.errorAlert).hide();
		plugin.emptyTrash();
	}

	// Empty the Trash
	plugin.emptyTrash = function(){
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : plugin.formAction,
				nonce : plugin.nonce,
				posttype : plugin.postType
			},
			success: function(data){
				plugin.loading(false);
				if (data.status === 'error'){
					$(plugin.errorAlert).text(data.message).show();
				} else {
					$(plugin.trashLinks).hide();
				}
			}
		});
	}

	// Loading Indication
	plugin.loading = function(loading){
		if ( loading ){
			$(plugin.loadingIndicator).show();
			return;
		}
		$(plugin.loadingIndicator).hide();
	}

	return plugin.init();
}
