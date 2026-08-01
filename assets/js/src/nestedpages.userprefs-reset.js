var NestedPages = NestedPages || {};

/**
* Reset User Preferences
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.UserPreferencesReset = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		resetButton : 'data-nestedpages-reset-user-prefs',
		resetForm : '.nestedpages-reset-user-prefs',
		formComplete : '.nestedpages-reset-user-prefs-complete'
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', '[' + plugin.selectors.resetButton + ']', function(e){
			e.preventDefault();
			plugin.resetPreferences();
		});
	}

	plugin.resetPreferences = function()
	{
		plugin.loading(true);
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.resetUserPrefs,
				nonce : NestedPages.jsData.nonce
			},
			success: function(data){
				plugin.loading(false);
				$(plugin.selectors.resetForm).hide();
				$(plugin.selectors.formComplete).show();
				if ( data.status !== 'success' ){
					console.log('There was an error clearing user preferences.');
				}
			}
		});
	}

	plugin.loading = function(loading)
	{
		if ( loading ){
			$('[' + plugin.selectors.resetButton + ']').attr('disabled', true);
			return;
		}
		$('[' + plugin.selectors.resetButton + ']').removeAttr('disabled');
	}

	return plugin.bindEvents();
}

new NestedPages.UserPreferencesReset;