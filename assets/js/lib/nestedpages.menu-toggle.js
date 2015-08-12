var NestedPages = NestedPages || {};

/**
* Toggles Menu Elements
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.MenuToggle = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.formatter = new NestedPages.Formatter;

	plugin.bindEvents = function()
	{
		$(document).on('click', NestedPages.selectors.childToggleLink, function(e){
			e.preventDefault();
			plugin.toggleSingleMenu($(this));
		});
		$(document).on('click', NestedPages.selectors.toggleAll, function(e){
			e.preventDefault();
			plugin.toggleAllMenus();
		});
	}


	// Toggle individual submenus
	plugin.toggleSingleMenu = function(button)
	{
		var submenu = $(button).parent(NestedPages.selectors.childToggle).parent(NestedPages.selectors.row).siblings('ol');
		$(button).find('i')
			.toggleClass(NestedPages.cssClasses.iconToggleDown)
			.toggleClass(NestedPages.cssClasses.iconToggleRight);
		$(submenu).toggle();
		plugin.formatter.setBorders();
		plugin.formatter.setNestedMargins();
		plugin.syncUserToggles();
	}


	// Toggle All Submenus
	plugin.toggleAllMenus = function()
	{
		var button = NestedPages.selectors.toggleAll;
		if ( $(button).attr('data-toggle') === 'closed' ){
			$(NestedPages.selectors.lists).show();
			$(button).attr('data-toggle', 'opened').text(NestedPages.jsData.collapseText);
			$(NestedPages.selectors.childToggle).find('i').removeClass(NestedPages.cssClasses.iconToggleRight).addClass(NestedPages.cssClasses.iconToggleDown);
			// revert_quick_edit();
			plugin.formatter.setBorders();
			plugin.syncUserToggles();
			return;
		}
		
		$(NestedPages.selectors.lists).not($(NestedPages.selectors.lists)[0]).hide();
		$(button).attr('data-toggle', 'closed').text(NestedPages.jsData.expandText);
		$(NestedPages.selectors.childToggle).find('i').removeClass(NestedPages.cssClasses.iconToggleDown).addClass(NestedPages.cssClasses.iconToggleRight);
		// revert_quick_edit();
		plugin.formatter.setBorders();
		plugin.syncUserToggles();
	}


	// Get an array of visible rows
	plugin.visibleRowIDs = function()
	{
		var visible_ids = [];
		var visible = $(NestedPages.selectors.rows + ':visible');
		$.each(visible, function(i, v){
			var id = $(this).attr('id');
			visible_ids.push(id.replace("menuItem_", ""));
		});
		return visible_ids;
	}


	// Save the user's toggled menus
	plugin.syncUserToggles = function()
	{
		$.ajax({
			url: NestedPages.jsData.ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action : NestedPages.formActions.syncToggles,
				nonce : NestedPages.jsData.nonce,
				ids : plugin.visibleRowIDs(),
				posttype : NestedPages.jsData.posttype
			},
			success: function(data){
				if ( data.status !== 'success' ){
					console.log('There was an error saving toggled pages.');
				}
			}
		});
	}


	return plugin.bindEvents();
}