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
			plugin.openModal();
		});
	}

	// Open the modal with clone options
	plugin.openModal = function()
	{
		console.log(plugin.parent_title);
		$(NestedPages.selectors.cloneModal).find('[data-clone-parent]').text(plugin.parent_title);
		$(NestedPages.selectors.cloneModal).modal('show');
	}


	// Clone the post
	// TODO: show modal with optional title and status, pass to handler/cloner
	plugin.clone = function()
	{
		plugin.toggleLoading(true);
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data : {
				action : NestedPages.formActions.clonePost,
				parent_id : plugin.parent_id,
				nonce : NestedPages.jsData.nonce
			},
			success : function(data){
				console.log(data);
				plugin.toggleLoading(false);
			}
		});
	}


	// Toggle Loading
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(NestedPages.selectors.errorDiv).hide();
			$(NestedPages.selectors.loadingIndicator).show();
			return;
		}
		$(NestedPages.selectors.loadingIndicator).hide();
	}

	return plugin.init();
}