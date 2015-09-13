/**
* Confirm deletion of a link
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
jQuery(document).ready(function(){
	new NestedPagesConfirmDelete;
});

var NestedPagesConfirmDelete = function()
{
	var plugin = this;
	var $ = jQuery;

	// DOM Selectors
	plugin.deleteButton = '[data-np-confirm-delete]'; // delete button
	plugin.confirmButton = '[data-delete-confirmation]'; // Confirm button in modal
	plugin.warningModal = '#np-delete-confirmation-modal'; // Modal with empty confirmation

	// JS Data
	plugin.deleteLink = ''; // Link for deleting the item

	// Initialization
	plugin.init = function(){
		plugin.bindEvents();
	}

	// Bind Events
	plugin.bindEvents = function(){
		$(document).on('click', plugin.deleteButton, function(e){
			e.preventDefault();
			plugin.deleteLink = $(this).attr('href');
			$(plugin.warningModal).modal('show');
		});
		$(document).on('click', plugin.confirmButton, function(e){
			e.preventDefault();
			plugin.confirmEmpty();
		});
	}

	// Confirm Trash Empty
	plugin.confirmEmpty = function(){
		window.location.replace(plugin.deleteLink);
	}

	return plugin.init();
}
