var NestedPages = NestedPages || {};

/**
* WPML functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Wpml = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.button = null; // The quick edit button with all the data-attributes for the post
	plugin.postData = null; // Object containing post data
	plugin.modal = null; // The modal element
	plugin.parent_li = null; // The post's nested pages list element
	plugin.formatter = new NestedPages.Formatter;

	plugin.selectors = {
		translationsBtn : 'data-nestedpages-translations',
		modal : 'data-wpml-translations-modal',
		title : 'data-wmpl-translation-title'
	}

	plugin.bindEvents = function()
	{
		if ( !nestedpages.wpml ) return;
		$(document).on('click', '[' + plugin.selectors.translationsBtn + ']', function(e){
			e.preventDefault();
			plugin.createTranslationsModal($(this));
		});
	}

	/**
	* Create the translations modal
	*/
	plugin.createTranslationsModal = function(button)
	{
		plugin.parent_li = $(button).closest(NestedPages.selectors.row).parent('li');
		plugin.button = $(button).siblings(NestedPages.selectors.quickEditOpen);
		plugin.postData = {
			id : $(plugin.button).attr('data-id'),
			title : $(plugin.button).attr('data-title'),
			slug : $(plugin.button).attr('data-slug')
		}
		plugin.modal = $('[' + plugin.selectors.modal + ']').clone();

		if ( $(plugin.parent_li).children('ol').length > 0 ){
			var child_ol = $(plugin.parent_li).children('ol');
			$(plugin.modal).insertBefore(child_ol);
		} else {
			$(plugin.modal).appendTo(plugin.parent_li);
		}
		$(plugin.modal).find('[' + plugin.selectors.title + ']').text(plugin.postData.title);
		plugin.formatter.showQuickEdit();
		$(plugin.modal).show();
		plugin.getTranslationData();
	}

	/**
	* Get the Translation Data for the Post
	*/
	plugin.getTranslationData = function()
	{
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data : {
				action : NestedPages.formActions.wpmlTranslations,
				post_id : plugin.postData.id,
				nonce : NestedPages.jsData.nonce
			},
			success: function(data){
				console.log(data);
				plugin.populateModal();
			}
		});
	}

	/**
	* Open the Modal
	*/
	plugin.populateModal = function()
	{
		plugin.toggleLoading(false);
	}

	/**
	* Toggle the Loading State
	*/
	plugin.toggleLoading = function(loading)
	{
		if ( loading ){
			$(plugin.modal).addClass('loading');
			return;
		}
		$(plugin.modal).removeClass('loading');
	}


	return plugin.bindEvents();
}