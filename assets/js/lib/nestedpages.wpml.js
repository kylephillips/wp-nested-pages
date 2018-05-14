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
		modal : 'data-np-wpml-translations-modal',
		title : 'data-wmpl-translation-title',
		table : 'data-np-wpml-translations-modal-table'
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
		plugin.parent_li = $(button).parents('.action-buttons').closest(NestedPages.selectors.row).parent('li');
		plugin.button = $(plugin.parent_li).find(NestedPages.selectors.quickEditOpen);
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
				console.log(plugin.postData.id);
				if ( data.status === 'success' ){
					plugin.populateModal(data.translations);
				} else {
					$(plugin.modal).find(NestedPages.selectors.quickEditErrorDiv).text(data.message).show();
					plugin.toggleLoading(false);
				}
			}
		});
	}

	/**
	* Open the Modal
	*/
	plugin.populateModal = function(translations)
	{
		var html = '<tbody>';
		$.each(translations, function(i, v){
			var translation = translations[i];
			html += '<tr>';
			html += '<td><img src="' + translation.country_flag_url + '" alt="' + translation.translated_name + '" /> ' + translation.translated_name + '</td>';
			html += '<td>';
			if ( translation.has_translation && translation.edit_link ){
				html += '<a href="' + translation.edit_link + '">' + translation.translation.post_title + ' (' + nestedpages.edit + ')</a>';
			} else {
				html += '<a href="' + translation.add_link + '" class="np-btn">+ ' + nestedpages.add_translation + '</a>';
			}
			html += '</td>';
			html += '</tr>';
		});
		html += '</tbody>';
		$(plugin.modal).find('[' + plugin.selectors.table + ']').html(html);
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