var NestedPages = NestedPages || {};

/**
* Confirm deletion of links
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.ConfirmDelete = function()
{
	var plugin = this;
	var $ = jQuery;

	// JS Data
	plugin.deleteLink = ''; // Link for deleting the item

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.linkDeleteButton, function(e){
			e.preventDefault();
			plugin.confirmSingle($(this));
		});
		$(document).on('click', NestedPages.selectors.linkDeleteConfirmationButton, function(e){
			e.preventDefault();
			if ( !$(this).hasClass('bulk') ){
				plugin.deleteSingle();
				return;
			}
			plugin.deleteMultiple();
		});
		$(document).on('submit', NestedPages.selectors.bulkActionsForm, function(e){
			plugin.confirmMultiple(e);
		});
	}

	// Confirm a single link deletion
	plugin.confirmSingle = function(button)
	{
		plugin.deleteLink = $(button).attr('href');
		$(NestedPages.selectors.linkDeleteConfirmationModalText).text(nestedpages.link_delete_confirmation_singular);
		$(NestedPages.selectors.linkDeleteConfirmationButton).text(nestedpages.delete).removeClass('bulk');
		$(document).trigger('open-modal-manual', NestedPages.selectors.linkDeleteConfirmationModal);
	}

	// Confirm Multiple link deletion
	plugin.confirmMultiple = function(event)
	{
		if ( $('select[name="np_bulk_action"]').val() !== 'trash' ) return;
		var linkCount = $(NestedPages.selectors.bulkActionRedirectIds).val();
		if ( linkCount === '' ) return;
		event.preventDefault();
		$(NestedPages.selectors.linkDeleteConfirmationModalText).text(nestedpages.link_delete_confirmation);
		$(NestedPages.selectors.linkDeleteConfirmationButton).text(nestedpages.trash_delete_links).addClass('bulk');
		$(document).trigger('open-modal-manual', [NestedPages.selectors.linkDeleteConfirmationModal]);
	}

	// Submit the form to delete multiple
	plugin.deleteMultiple = function()
	{
		$(NestedPages.selectors.bulkActionsForm)[0].submit();
	}

	// Delete the single
	plugin.deleteSingle = function()
	{
		window.location.replace(plugin.deleteLink);
	}

	return plugin.bindEvents();
}
