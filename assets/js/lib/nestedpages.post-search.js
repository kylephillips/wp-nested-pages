var NestedPages = NestedPages || {};

/**
* Perform an AJAX search for posts by type
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.PostSearch = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		input : 'data-nestedpages-post-search',
		form : 'data-nestedpages-post-search-form',
		loadingIndicator : 'data-nestedpages-loading',
		noResults : 'data-nestedpages-no-results',
		results: 'data-nestedpages-search-results'
	}

	plugin.changed = false;
	plugin.activeInput = ''; // The active input
	plugin.results = ''; // Search results
	plugin.defaultResults = ''; // The default, loaded results
	plugin.postType = ''; // The type of search (post_type, taxonomy)
	plugin.activeForm = '';

	plugin.bindEvents = function()
	{
		$('['+ plugin.selectors.input + ']').on('input', function(){
			plugin.activeInput = $(this);
			plugin.setOptions();
			if ( !plugin.changed ) plugin.setDefaultResults();
			if ( $(this).val() === '' ) {
				$(plugin.activeForm).find('[' + plugin.selectors.noResults + ']').hide();
				plugin.showDefaultResults();
				return;
			}
			plugin.query();
		});
	}

	/**
	* Set the default results 
	*/
	plugin.setDefaultResults = function()
	{
		plugin.defaultResults = $(plugin.activeForm).find('[' + plugin.selectors.results + ']').html();
		plugin.changed = true;
	}

	/**
	* Show the default results 
	*/
	plugin.showDefaultResults = function()
	{
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').html(plugin.defaultResults);
	}

	/**
	* Set the options
	*/
	plugin.setOptions = function()
	{
		plugin.postType = $(plugin.activeInput).attr(plugin.selectors.input);
		plugin.activeForm = $(plugin.activeInput).parents('[' + plugin.selectors.form + ']');
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
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').empty();
		plugin.toggleLoading(true);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.postSearch,
				nonce : NestedPages.jsData.nonce,
				term : $(plugin.activeInput).val(),
				postType : plugin.postType
			},
			success: function(data){
				if ( data.results ){
					plugin.results = data.results;
					plugin.loadResults();
					plugin.toggleLoading(false);
				} else {
					plugin.toggleLoading(false);
					$(plugin.activeForm).find('[' + plugin.selectors.noResults + ']').show();
				}
			}
		});
	}

	// Load the results into view
	plugin.loadResults = function()
	{
		var html = "<ul>";
		$.each(plugin.results, function(i, v){
			html += '<li><a href="#" data-assignment-page-id="' + v.ID + '" data-assignment-page-title="' + v.post_title + '">' + v.post_title + '</a></li>';
		});
		html += '</ul>';
		$(plugin.activeForm).find('[' + plugin.selectors.results + ']').html(html);
		plugin.toggleLoading(false);
	}

	// Toggle the loading indicator
	plugin.toggleLoading = function(loading)
	{
		var loadingIndicator = $(plugin.activeForm).find('[' + plugin.selectors.loadingIndicator + ']');
		var noResults = $(plugin.activeForm).find('[' + plugin.selectors.noResults + ']');
		$(noResults).hide();
		if ( loading ){
			$(loadingIndicator).show();
			return;
		}
		$(loadingIndicator).hide();
	}

	return plugin.bindEvents();
}