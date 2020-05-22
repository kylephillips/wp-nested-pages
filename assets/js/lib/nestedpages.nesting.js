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
		if ( !NestedPages.jsData.nestable ) return plugin.initializeSortableFlat();

		$(NestedPages.selectors.sortable).not(NestedPages.selectors.notSortable).nestedSortable({
			items : NestedPages.selectors.rows,
			toleranceElement: '> .row',
			handle: NestedPages.selectors.handle,
			placeholder: "ui-sortable-placeholder",
			tabSize : 56,
			isAllowed: function(placeholder, placeholderParent, currentItem){
				return ( $(placeholderParent).hasClass('post-type-np-redirect') && !$(currentItem).hasClass('post-type-np-redirect') ) ? false : true;
			},
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

	// Initialize Flat Sortable (Non-Hierarchical Post Types)
	plugin.initializeSortableFlat = function()
	{
		var lists = $(NestedPages.selectors.lists).not(NestedPages.selectors.notSortable);
		$.each(lists, function(){
			$(this).sortable({
				items : '>' + NestedPages.selectors.rows,
				handle: NestedPages.selectors.handle,
				placeholder: "ui-sortable-placeholder",
				forcePlaceholderSize: true,
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
		var list,
		filtered;

		if ( nestedpages.manual_order_sync === '1' && !manual) return;
		$(NestedPages.selectors.errorDiv).hide();
		$(NestedPages.selectors.loadingIndicator).show();
		filtered = ( $(NestedPages.selectors.lists).first().hasClass('filtered') ) ? true : false;
		if ( NestedPages.jsData.nestable && !filtered ){
			list = $(NestedPages.selectors.sortable).nestedSortable('toHierarchy', {startDepthCount: 0});
		} else {
			list = plugin.setNestingArray();
		}
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
				syncmenu : syncmenu,
				filtered : filtered
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

	plugin.setNestingArray = function(list)
	{
		ret = [];
		$(NestedPages.selectors.lists).first().children('li.page-row').each(function() {
			var level = plugin.recursiveNesting(this);
			ret.push(level);
		});
		return ret;
	}

	plugin.recursiveNesting = function(item) {
		var id = $(item).attr('id');
		var currentItem;
		if (id) {
			id = id.replace('menuItem_', '');
			currentItem = {
				"id": id
			};
			if ($(item).children(NestedPages.selectors.lists).children(NestedPages.selectors.rows).length > 0) {
				currentItem.children = [];
				$(item).children(NestedPages.selectors.lists).children(NestedPages.selectors.rows).each(function() {
					var level = plugin.recursiveNesting(this);
					currentItem.children.push(level);
				});
			}
			return currentItem;
		}
	}
}