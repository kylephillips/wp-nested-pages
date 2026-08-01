var NestedPages = NestedPages || {};

/**
* Trash post with all children
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.TrashWithChildren = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.post_id = ''; // The parent/source post ID

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.trashWithChildrenButton, function(e){
			e.preventDefault();
			plugin.post_id = $(this).attr('data-post-id');
			plugin.trash();
		});
	}

	// Trash the posts
	plugin.trash = function()
	{
		$.ajax({
			url : NestedPages.jsData.ajaxurl,
			type : 'post',
			data : {
				action : NestedPages.formActions.trashWithChildren,
				nonce : NestedPages.jsData.nonce,
				post_id : plugin.post_id,
				screen : nestedpages.current_page
			},
			success : function(data){
				window.location.replace(data.redirect);
			}, error : function(data){
				console.log(data);
			}
		});
	}

	return plugin.bindEvents();
}