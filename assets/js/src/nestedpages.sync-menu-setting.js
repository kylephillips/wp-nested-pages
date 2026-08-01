var NestedPages = NestedPages || {};

/**
* Sync the "sync menu" setting
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.SyncMenuSetting = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	plugin.init = function()
	{
		plugin.bindEvents();
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){ // catches trash updates
			if ( nestedpages.manual_menu_sync === '1' ) return;
			if ( nestedpages.syncmenu === '1' ) plugin.syncSetting(); 
		});
		$(document).on('change', NestedPages.selectors.syncCheckbox, function(){
			plugin.syncSetting();
		});
	}

	// Sync the "Sync menu" preference / setting
	plugin.syncSetting = function()
	{

		if ( NestedPages.jsData.posttype !== 'page' ) return;
		if ($(NestedPages.selectors.syncCheckbox).length === 0) return;
		
		NestedPages.jsData.syncmenu = ( $(NestedPages.selectors.syncCheckbox).is(':checked') ) ? 'sync' : 'nosync';

		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncMenu,
				nonce : NestedPages.jsData.nonce,
				post_type : NestedPages.jsData.posttype,
				syncmenu : NestedPages.jsData.syncmenu
			},
			success: function(data){
				if (data.status === 'error'){
					plugin.formatter.showAjaxError(data.message);
				}
			},
			error: function(data){
				console.log(data);
			}
		});
	}

	return plugin.bindEvents();
}