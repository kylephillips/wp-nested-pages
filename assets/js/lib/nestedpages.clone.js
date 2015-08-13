var NestedPages = NestedPages || {};

/**
* Post clone functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Clone = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.parent_id = ''; // The parent/source post ID
	plugin.parent_title = ''; // The parent title
	plugin.parentLi = null;

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}


	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.cloneButton, function(e){
			e.preventDefault();
			plugin.parent_id = $(this).attr('data-id');
			plugin.parent_title = $(this).attr('data-parentname');
			plugin.parentLi = $(this).parent('.row').parent('.page-row').parent('.npList');
			plugin.openModal();
		});
		$(document).on('click', NestedPages.selectors.confirmClone, function(e){
			e.preventDefault();
			plugin.clone();
		});
	}

	// Open the modal with clone options
	plugin.openModal = function()
	{
		$(NestedPages.selectors.cloneModal).find('[data-clone-parent]').text(plugin.parent_title);
		$(NestedPages.selectors.cloneModal).modal('show');
	}


	// Clone the post
	plugin.clone = function()
	{
		plugin.toggleLoading(true);
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data : {
				action : NestedPages.formActions.clonePost,
				parent_id : plugin.parent_id,
				quantity : $(NestedPages.selectors.cloneQuantity).val(),
				status : $(NestedPages.selectors.cloneStatus).val(),
				author : $(NestedPages.selectors.cloneAuthor).find('select').val(),
				nonce : NestedPages.jsData.nonce,
				posttype : NestedPages.jsData.posttype
			},
			success : function(data){
				plugin.toggleLoading(false);
				$(NestedPages.selectors.cloneModal).modal('hide');
				location.reload();
			}
		});
	}


	// Toggle Loading
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.cloneModal).find('[data-clone-loading]').show();
			$(NestedPages.selectors.confirmClone).attr('disabled', 'disabled');
			return;
		}
		$(NestedPages.selectors.cloneModal).find('[data-clone-loading]').hide();
		$(NestedPages.selectors.confirmClone).attr('disabled', false);
	}

	return plugin.init();
}