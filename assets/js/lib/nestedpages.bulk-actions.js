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
	plugin.selectedLinks = [];
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
				plugin.toggleBulkEdit(true);
			}
		});
		$(document).on('click', NestedPages.selectors.bulkEditRemoveItem, function(e){
			e.preventDefault();
			var id = $(this).siblings('input[type=hidden]').val();
			plugin.uncheckBulkItem(id);
		});
		$(document).on('click', NestedPages.selectors.bulkEditCancel, function(e){
			e.preventDefault();
			plugin.uncheckAllBulkItems();
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
		plugin.selectedLinks = [];
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			if ( $(this).is(':checked') ) {
				var row = $(this).parents(NestedPages.selectors.rows);
				checked = true;
				if ( !$(this).parent('div').hasClass('np-check-all') && !$(row).hasClass('post-type-np-redirect') ) checked_ids += $(this).val() + ',';
				if ( $(row).hasClass('post-type-np-redirect') ) {
					checked_redirect_ids += $(this).val() + ',';
					plugin.selectedLinks.push($(this).val());
				}
				if ( $(this).attr('data-np-post-type') !== 'np-redirect' && !$(this).parent('div').hasClass('np-check-all') ){
					var post = [];
					post['title'] = $(this).attr('data-np-bulk-checkbox');
					post['id'] = $(this).val();
					plugin.selectedPosts.push(post);
				}
			}
		});
		plugin.setBulkEditPosts();
		plugin.toggleEditOption();
		plugin.toggleLinkCountAlert();
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
			html += '<input type="hidden" name="post_ids[]" value="' + plugin.selectedPosts[i].id + '"></li>';
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
			plugin.toggleBulkEdit(false);
		}
	}

	/**
	* Uncheck all bulk items
	*/
	plugin.uncheckAllBulkItems = function()
	{
		$.each($(NestedPages.selectors.bulkActionsCheckbox), function(){
			$(this).prop('checked', false).change();
		});
		plugin.toggleBulkEdit(false);
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

	/**
	* Toggle the edit option to disabled if no post checkboxes are checked
	* Prevents opening the bulk edit form with only np-redirects checked
	*/
	plugin.toggleEditOption = function()
	{
		var checkedLength = $(NestedPages.selectors.bulkActionsCheckbox + ':checked').not('.np-check-all input').not('.np-redirect-bulk').length;
		var option = $(NestedPages.selectors.bulkActionsForm).find('select option[value=edit]');
		if ( checkedLength === 0 ){
			$(option).prop('disabled', true);
			$(NestedPages.selectors.bulkActionsForm).find('select option').first().prop('selected', true);
			plugin.toggleBulkEdit(false);
			return;
		}
		$(option).prop('disabled', false);
	}

	/**
	* Toggle the bulk edit form
	*/
	plugin.toggleBulkEdit = function(visible)
	{
		plugin.toggleLinkCountAlert();
		if ( visible ){
			$(NestedPages.selectors.bulkEditForm).show();
			$(NestedPages.selectors.bulkActionsForm).hide();
			plugin.setWPSuggest();
			return;
		}
		$(NestedPages.selectors.bulkEditForm).hide();
		$(NestedPages.selectors.bulkActionsForm).show();
		$(NestedPages.selectors.bulkEditLinkCount).parent('div').hide();
		$(NestedPages.selectors.bulkActionsForm).find('select option').first().text(nestedpages.bulk_actions);
		plugin.resetBulkEditFields();
	}

	/**
	* Toggle the bulk edit link count alert
	*/
	plugin.toggleLinkCountAlert = function()
	{
		var selectedLinkCount = plugin.selectedLinks.length;
		if ( selectedLinkCount === 0 ) {
			$(NestedPages.selectors.bulkEditLinkCount).parent('div').hide();
			return;
		}
		$(NestedPages.selectors.bulkEditLinkCount).parent('div').show();
	}

	/**
	* Initialize WP Auto Suggest on Flat Taxonomy fields
	*/
	plugin.setWPSuggest = function()
	{
		var tagfields = $(NestedPages.selectors.bulkEditForm).find('[data-autotag]');
		$.each(tagfields, function(i, v){
			var taxonomy = $(this).attr('data-taxonomy');
			$(this).suggest(ajaxurl + '?action=ajax-tag-search&tax=' + taxonomy , {multiple:true, multipleSep: ","});
		});
	}

	/**
	* Clear out the bulk edit fields
	*/
	plugin.resetBulkEditFields = function()
	{
		var selectFields = $(NestedPages.selectors.bulkEditForm).find('select');
		$.each(selectFields, function(){
			$(this).find('option').first().prop('selected', true);
		});
		var categoryChecklists = $(NestedPages.selectors.bulkEditForm).find('.cat-checklist');
		$.each(categoryChecklists, function(){
			$(this).find('input[type=checkbox]').prop('checked', false);
		});
	}

	return plugin.init();
}