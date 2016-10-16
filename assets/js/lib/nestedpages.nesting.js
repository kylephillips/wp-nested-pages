var NestedPages = NestedPages || {};

/**
* Enables and Saves Nesting
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.Nesting = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;


	// Make the Menu sortable
	plugin.initializeSortable = function()
	{
		maxLevels = ( NestedPages.jsData.nestable ) ? 0 : 1;
		$(NestedPages.selectors.sortable).not(NestedPages.selectors.notSortable).nestedSortable({
			items : NestedPages.selectors.rows,
			toleranceElement: '> .row',
			handle: NestedPages.selectors.handle,
			placeholder: "ui-sortable-placeholder",
			maxLevels: maxLevels,
			tabSize : 56,
			start: function(e, ui){
        		ui.placeholder.height(ui.item.height());
    		},
    		sort: function(e, ui){
    			plugin.formatter.updatePlaceholderWidth(ui);
    		},
    		stop: function(e, ui){
    			setTimeout(
    				function(){
    					plugin.formatter.updateSubMenuToggle();
    					plugin.formatter.setBorders();
    					plugin.formatter.setNestedMargins();
    				}, 100
    			);
    			plugin.syncNesting();
    		},
		});
	}


	// Disable Nesting
	plugin.disableNesting = function()
	{
		$(NestedPages.selectors.sortable).sortable('destroy');
	}


	// Sync Nesting
	plugin.syncNesting = function(manual, callback)
	{
		if ( nestedpages.manual_order_sync === '1' && !manual) return;
		$(NestedPages.selectors.errorDiv).hide();
		$(NestedPages.selectors.loadingIndicator).show();

		list = $(NestedPages.selectors.sortable).nestedSortable('toHierarchy', {startDepthCount: 0});
		plugin.disableNesting();

		var syncmenu = NestedPages.jsData.syncmenu;
		if ( nestedpages.manual_menu_sync === '1' ) syncmenu = 'nosync';

		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncNesting,
				nonce : NestedPages.jsData.nonce,
				list : list,
				post_type : NestedPages.jsData.posttype,
				syncmenu : syncmenu
			},
			success: function(data, callback){
				plugin.initializeSortable();
				if (data.status === 'error'){
					$(NestedPages.selectors.errorDiv).text(data.message).show();
					$(NestedPages.selectors.loadingIndicator).hide();
				} else {
					if ( callback && typeof callback === 'function') {
						callback();
						return;
					}
					$(NestedPages.selectors.loadingIndicator).hide();
				}
			}
		});
	}

}