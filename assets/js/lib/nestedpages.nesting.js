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

	let ajaxLimit = 200;
	let ajaxIndex = 0;
	let ajaxLists = [];

	// Make the Menu sortable
	plugin.initializeSortable = function()
	{
		if ( !NestedPages.jsData.nestable ) return plugin.initializeSortableFlat();
		var maxLevels = 0;

		// Set the max level if necessary
		if ( typeof nestedpages.post_types[NestedPages.jsData.posttype] !== 'undefined' ){
			var post_type = nestedpages.post_types[NestedPages.jsData.posttype];
			if ( typeof post_type.enable_max_nesting !== 'undefined' && post_type.enable_max_nesting && typeof post_type.maximum_nesting !== 'undefined' && post_type.maximum_nesting > 1 ) maxLevels = post_type.maximum_nesting;
		}

		$(NestedPages.selectors.sortable).not(NestedPages.selectors.notSortable).nestedSortable({
			items : NestedPages.selectors.rows,
			toleranceElement: '> .row',
			handle: NestedPages.selectors.handle,
			placeholder: "ui-sortable-placeholder",
			tabSize : 56,
			maxLevels : maxLevels,
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

		// Flatten and chunk the list to avoid max input vars errors
		
		let listFlat = plugin.flattenList(list, 0, 0);

		ajaxIndex = 0;
		ajaxLists = [];

		for (let i = 0; i < Math.ceil(listFlat.length / ajaxLimit); i++) {
			let start = (i * ajaxLimit);

			ajaxLists.push(listFlat.slice(start, start + ajaxLimit));
		}

		plugin.doAjax(syncmenu, filtered);
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

	// Flatten the list and include new order

	plugin.flattenList = function(list, depth, parent) {
		let final = [];

		$.each(list, (index, item) => {
			final.push({
				id: item.id,
				parent_id: parent,
				depth: depth + 1,
				order: index + 1,
			});

			if (item.children) {
				let children = plugin.flattenList(item.children, depth + 1, item.id);

				final = final.concat(children);
			}
		});

		return final;
	}
	
	// Sync chunks
	plugin.doAjax = function(syncmenu, filtered) {
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncNesting,
				nonce : NestedPages.jsData.nonce,
				list: ajaxLists[ajaxIndex],
				post_type : NestedPages.jsData.posttype,
				syncmenu : syncmenu,
				filtered : filtered
			},
			success: function(data, callback){
				if (data.status === 'error'){
					$(NestedPages.selectors.errorDiv).text(data.message).show();
					$(NestedPages.selectors.loadingIndicator).hide();
				} else {
					// Callback not used?
					// if ( callback && typeof callback === 'function') {
					// 	callback();
					// 	return;
					// }
					// $(NestedPages.selectors.loadingIndicator).hide();

					ajaxIndex++;

					if (ajaxIndex >= ajaxLists.length) {
						plugin.syncComplete();
					} else {
						plugin.doAjax(syncmenu, filtered);
					}
				}
			}
		});
	}

	// All done!
	plugin.syncComplete = function() {
		plugin.initializeSortable();
		$(NestedPages.selectors.loadingIndicator).hide();

		ajaxIndex = 0;
		ajaxLists = [];
	}

}