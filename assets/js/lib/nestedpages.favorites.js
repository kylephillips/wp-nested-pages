/**
* Empty Trash Functionality
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
jQuery(document).ready(function(){
	new NestedPagesFavorites;
});

var NestedPagesFavorites = function()
{
	var plugin = this;
	var $ = jQuery;

	// DOM Selectors
	plugin.errorAlert = '#np-error'; // Alert Error Notification
	plugin.loadingIndicator = '#nested-loading'; // Loading Indication
	plugin.toggleFavoriteButton = '.np-toggle-favorite-checkbox'; // Button to toggle if this page is favorite
	plugin.listItemPrefix = '#menuItem_'; // Prefix of list items

	// JS Data
	plugin.nonce = nestedpages.np_nonce;
	plugin.formAction = 'npUpdateFavorites';


	// Initialization
	plugin.init = function(){
		plugin.bindEvents();
	}

	// Bind Events
	plugin.bindEvents = function(){
		$(document).on('change', plugin.toggleFavoriteButton, function(e){
			e.preventDefault();
			plugin.toggleFavorite($(this));
		});
	}

	// Toggle favorite
	plugin.toggleFavorite = function(checkbox)
	{
		var enableFavorite = $(checkbox).is(':checked');
		var dataID = $(checkbox).data("id");

		plugin.loading(true);
		plugin.updateFavoriteData(dataID, enableFavorite);
	}

	// Update html
	plugin.updatePostRowsFavoriteStatus = function(dataID, enableFavorite, totalNumberOfFavorites)
	{
		if(enableFavorite){
			//Add the 'favorite' class and set the checked property to all ancestors and to the selected item.
			var changedElement = $(plugin.listItemPrefix+dataID);
			$(changedElement).addClass("favorite");
			$(changedElement).find("input.np-toggle-favorite-checkbox").first().prop('checked', enableFavorite);
			$(changedElement).parents("li.page-row").addClass("favorite");
			$(changedElement).parents("li.page-row").each(function(index){
				$(this).find("input.np-toggle-favorite-checkbox").first().prop('checked', enableFavorite);
			});
		}
		else {
			//Remove the 'favorite' class and set the checked property to all children and to the selected item.
			var changedElement = $(plugin.listItemPrefix+dataID);
			$(changedElement).removeClass("favorite");
			$(changedElement).find("input.np-toggle-favorite-checkbox").prop('checked', enableFavorite);
			$(changedElement).find("li.page-row").removeClass("favorite");
		}

		//Update the total favorites count
		var favoritesText = $('a[href="#favorite"]').text();
		favoritesText = favoritesText.replace(/\(.*?\)/, "("+totalNumberOfFavorites+")");
		$('a[href="#favorite"]').text(favoritesText);

		//Update favorites if we're on that filter.
		if($('a[href="#favorite"]').hasClass("active"))
			$('a[href="#favorite"]').click();
	}

	// Update favorite user meta data
	plugin.updateFavoriteData = function(dataID, enableFavorite)
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data : {
				action : plugin.formAction,
				nonce : plugin.nonce,
				id : dataID,
				newStatus : enableFavorite
			},
			success: function(data){
				plugin.loading(false);
				plugin.updatePostRowsFavoriteStatus(dataID, enableFavorite, data.message);
			}
		});
	}

	// Loading Indication
	plugin.loading = function(loading){
		if ( loading ){
			$(plugin.loadingIndicator).show();
			return;
		}
		$(plugin.loadingIndicator).hide();
	}

	return plugin.init();
}
