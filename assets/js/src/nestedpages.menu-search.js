var NestedPages = NestedPages || {};

/**
* Menu Item Search in Modal Link Form
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuSearch = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		searchForms : '*[data-np-menu-search]', // Search form selector
		defaultResults : '[data-default-result]', // Default results list items
		loadingIndicator : '.np-menu-search-loading', // loading indicator
		noResults : '.np-menu-search-noresults', // No results
		searchType : 'data-search-type', // The search object type (post_type, taxonomy)
		searchObject : 'data-search-object', // The object to search (post, category, etc)
		searchResults : '[data-np-search-result]', // Appended search result rows
	}

	plugin.activeForm = ''; // The active form
	plugin.results = ''; // Search results
	plugin.defaultResults = ''; // The default, loaded results
	plugin.searchType = ''; // The type of search (post_type, taxonomy)
	plugin.searchObject = ''; // The object being searched (post, category, post_tag, etc…)

	plugin.formatter = new NestedPages.Formatter;

	plugin.bindEvents = function()
	{
		$(document).on('keyup', plugin.selectors.searchForms, function(){
			plugin.activeForm = $(this);
			$(plugin.selectors.searchResults).remove();
			plugin.performSearch();
		});
	}


	// Perform the search
	plugin.performSearch = function()
	{
		plugin.defaultResults = $(plugin.activeForm).parent('li').siblings(plugin.selectors.defaultResults);
		if ( $(plugin.activeForm).val().length > 2 ){
			$(plugin.defaultResults).hide();
			plugin.toggleLoading(true);
			plugin.query();
			return;
		}
		plugin.toggleLoading(false);
		$(plugin.defaultResults).show();
	}


	// Query Search
	plugin.query = function()
	{
		plugin.searchType = $(plugin.activeForm).attr(plugin.selectors.searchType);
		plugin.searchObject = $(plugin.activeForm).attr(plugin.selectors.searchObject);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.search,
				nonce : NestedPages.jsData.nonce,
				term : $(plugin.activeForm).val(),
				searchType : plugin.searchType,
				searchObject : plugin.searchObject,
			},
			success: function(data){
				if ( data.results ){
					plugin.results = data.results;
					plugin.toggleLoading(false);
					if ( plugin.searchType === 'post_type' ){
						plugin.appendPosts();
					} else {
						plugin.appendTaxonomies();
					}
				} else {
					plugin.toggleLoading(false);
					$(plugin.activeForm).siblings(plugin.selectors.noResults).show();
				}
			}
		});
	}


	// Append post type results
	plugin.appendPosts = function()
	{
		$('[data-np-search-result]').remove();
		var html = "";
		$.each(plugin.results, function(i, v){
			html = '<li data-np-search-result><a href="#" data-np-menu-object="' + plugin.searchObject + '" data-np-menu-type="post_type" data-np-menu-objectid="' + v.ID + '" data-np-permalink="' + v.permalink + '" data-np-object-name="' + v.singular_name + '" data-np-menu-selection></a></li>';
			$(html).insertAfter($(plugin.activeForm).parent('li'));
			$('[data-np-menu-objectid="' + v.ID + '"').text(v.post_title);
		});
		plugin.toggleLoading(false);
	}


	// Append taxonomy results
	plugin.appendTaxonomies = function()
	{
		var html = "";
		$.each(plugin.results, function(i, v){
			html += '<li data-np-search-result><a href="#" data-np-menu-object="' + plugin.searchObject + '" data-np-menu-type="post_type" data-np-menu-objectid="' + v.term_id + '" data-np-permalink="' + v.permalink + '" data-np-object-name="' + v.taxonomy + '" data-np-menu-selection>' + v.name + '</a></li>';
		});
		$(html).insertAfter($(plugin.activeForm).parent('li'));
		plugin.toggleLoading(false);
	}


	// Toggle the loading indicator
	plugin.toggleLoading = function(loading)
	{
		var loadingIndicator = $(plugin.activeForm).siblings(plugin.selectors.loadingIndicator);
		$(plugin.selectors.noResults).hide();
		if ( loading ){
			$(loadingIndicator).show();
			return;
		}
		$(loadingIndicator).hide();
	}

	return plugin.bindEvents();
}