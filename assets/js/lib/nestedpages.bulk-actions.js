var NestedPages = NestedPages || {};

/**
* Bulk Actions for Nested View
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.BulkActions = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectedNumber = 0;
	plugin.selectedPosts = []; // array

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).on('change', NestedPages.selectors.bulkActionsCheckbox, function(){
			plugin.toggleBulkForm();
		});
		$(document).on('submit', NestedPages.selectors.bulkActionsForm, function(e){
			if ( $('select[name=np_bulk_action]').val() === 'edit' ){
				e.preventDefault();
				$(NestedPages.selectors.bulkEditForm).show();
			}
		});
		$(document).on('click', NestedPages.selectors.bulkEditRemoveItem, function(e){
			e.preventDefault();
			var id = $(this).siblings('input[type=hidden]').val();
			plugin.uncheckBulkItem(id);
		});
	}

	/**
	* Toggle the Bulk Actions Form & Populate the Hidden ID Fields for posts and redirects
	*/
	plugin.toggleBulkForm = function()
	{
		var checked = false;
		var checked_ids = '';
		var checked_redirect_ids = '';
		plugin.selectedPosts = [];
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				var row = $(this).parents(NestedPages.selectors.rows);
				checked = true;
				if ( !$(this).parent('div').hasClass('np-check-all') && !$(row).hasClass('post-type-np-redirect') ) checked_ids += $(this).val() + ',';
				if ( $(row).hasClass('post-type-np-redirect') ) checked_redirect_ids += $(this).val() + ',';
				if ( $(this).attr('data-np-post-type') !== 'np-redirect' && !$(this).parent('div').hasClass('np-check-all') ){
					var post = [];
					post['title'] = $(this).attr('data-np-bulk-checkbox');
					post['id'] = $(this).val();
					plugin.selectedPosts.push(post);
				}
			}
		});
		plugin.setBulkEditPosts();
		if ( checked ){
			$(NestedPages.selectors.bulkActionsForm).show();
			$(NestedPages.selectors.bulkActionsIds).val(checked_ids);
			$(NestedPages.selectors.bulkActionRedirectIds).val(checked_redirect_ids);
			plugin.setSelectedNumber();
			return;
		}
		$(NestedPages.selectors.bulkActionsIds).val('');
		$(NestedPages.selectors.bulkActionsForm).hide();
	}

	/**
	* Set the Posts for Bulk Edit
	*/
	plugin.setBulkEditPosts = function()
	{
		var html = '';
		for ( var i = 0; i < plugin.selectedPosts.length; i++ ){
			html += '<li><a href="#" class="np-remove" data-np-remove-bulk-item>&times;</a>';
			html += plugin.selectedPosts[i].title;
			html += '<input type="hidden" name="bulk_id[]" value="' + plugin.selectedPosts[i].id + '"></li>';
		}
		$(NestedPages.selectors.bulkEditTitles).html(html);
	}

	/**
	* Uncheck a bulk item
	*/
	plugin.uncheckBulkItem = function(id)
	{
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).val() == id ) {
				$(this).prop('checked', false).change();
			}
		});
		// Hide the form if all posts are removed
		if ( $(NestedPages.selectors.bulkEditRemoveItem).length === 0 ){
			$(NestedPages.selectors.bulkEditForm).hide();
		}
	}

	/**
	* Set the number of total selected
	*/
	plugin.setSelectedNumber = function()
	{
		var checkedLength = $(NestedPages.selectors.bulkActionsCheckbox + ':checked').not('.np-check-all input').length;
		var option = $(NestedPages.selectors.bulkActionsForm).find('select option').first();
		$(option).text(nestedpages.bulk_actions + ' (' + checkedLength + ')');
	}

	return plugin.init();
}