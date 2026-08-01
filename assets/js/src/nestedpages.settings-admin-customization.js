var NestedPages = NestedPages || {};

/**
* Admin Customization Settings
* @package Nested Pages
* @author Kyle Phillips - https://github.com/kylephillips/wp-nested-pages
*/
NestedPages.AdminCustomizationSettings = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.selectors = {
		navItemCheckbox : 'data-nestedpages-admin-nav-item-checkbox',
		adminNavList : 'data-np-sortable-admin-nav',
		adminNavRoleSelect : 'data-np-nav-menu-user-role-select',
		adminNavRoleMenu : 'data-np-nav-menu-customization',
		adminNavDetails : 'data-np-extra-options',
		adminNavDetailsToggle : 'data-np-extra-options-button',
		adminSubNavList : 'data-np-sortable-admin-subnav',
		submenuToggle : 'data-np-nav-menu-customization-submenu-toggle',
		separatorRow : 'data-np-separator-row',
		separatorRemoveButton : 'data-np-remove-separator-button',
		separatorAddButton : 'data-np-add-separator-button'
	}

	plugin.bindEvents = function()
	{
		$(document).ready(function(){
			plugin.enableSortableAdminSorting();
			plugin.defaultAdminMenuRoleSelect();
		});
		$(document).on('change', '[' + plugin.selectors.navItemCheckbox + ']', function(e){
			plugin.toggleNavItemVisibility($(this));
		});
		$(document).on('change', '[' + plugin.selectors.adminNavRoleSelect + ']', function(e){
			var menu = $(this).val();
			plugin.toggleNavRoleMenu(menu);
		});	
		$(document).on('click', '[' + plugin.selectors.adminNavDetailsToggle + ']', function(e){
			e.preventDefault();
			plugin.toggleNavExtraOptions($(this));
		});
		$(document).on('click', '[' + plugin.selectors.submenuToggle + ']', function(e){
			e.preventDefault();
			plugin.toggleSubmenu($(this));
		});
		$(document).on('click', '[' + plugin.selectors.separatorRemoveButton + ']', function(e){
			e.preventDefault();
			plugin.removeSeparator($(this));
		});
		$(document).on('click', '[' + plugin.selectors.separatorAddButton + ']', function(e){
			e.preventDefault();
			plugin.addSeparator();
		});
	}

	plugin.enableSortableAdminSorting = function()
	{
		$('[' + plugin.selectors.adminNavList + ']').sortable({
			handle: '.handle',
			items: '.np-nav-preview',
			stop : function(event, ui){
				var items = $('[' + plugin.selectors.adminNavList + '] li');
				$.each(items, function(){
					$(this).find('[data-np-menu-order]').val($(this).index());
				});
			}
		});

		$('[' + plugin.selectors.adminSubNavList + ']').sortable({
			handle: '.handle',
			items: '.submenu-item',
			stop : function(event, ui){
				var items = $('[' + plugin.selectors.adminSubNavList + '] li');
				$.each(items, function(){
					$(this).find('[data-np-submenu-order]').val($(this).index());
				});
			}
		});
	}

	plugin.defaultAdminMenuRoleSelect = function()
	{
		$('[' + plugin.selectors.adminNavRoleSelect + ']').find('option:eq(0)').prop('selected', true);
	}

	/**
	* Toggle the Menu Item visibility under admin menu options (hide from menu)
	*/
	plugin.toggleNavItemVisibility = function(checkbox)
	{
		var item = $(checkbox).closest('li.np-nav-preview');
		var checked = ( $(checkbox).is(':checked') ) ? true : false;
		if ( checked ){
			$(item).addClass('disabled');
			return;
		}
		$(item).removeClass('disabled');
	}

	/**
	* Toggle which role's menu to edit
	*/
	plugin.toggleNavRoleMenu = function(menu)
	{
		$('[' + plugin.selectors.adminNavRoleMenu + ']').hide();
		var visibleMenu = $('[' + plugin.selectors.adminNavRoleMenu + '="' + menu + '"]');
		$('[' + plugin.selectors.adminNavRoleSelect + ']').val(menu);
		$(visibleMenu).show();
	}

	/**
	* Toggle the extra options for a nav menu item
	*/
	plugin.toggleNavExtraOptions = function(button)
	{
		var options = $(button).parents('.np-nav-preview').find('[' + plugin.selectors.adminNavDetails + ']');
		$(button).parents('.np-nav-preview').toggleClass('options-open');
	}

	/**
	* Toggle an admin submenu
	*/
	plugin.toggleSubmenu = function(button)
	{
		$(button).toggleClass('active');
		$(button).parents('li').toggleClass('submenu-open');
	}

	/**
	* Remove a separator
	*/
	plugin.removeSeparator = function(button)
	{
		$(button).closest('[' + plugin.selectors.separatorRow + ']').fadeOut(function(){
			$(this).remove();
		});
	}

	/**
	* Add a separator
	*/
	plugin.addSeparator = function()
	{
		var currentList = $('[' + plugin.selectors.adminNavList + ']:visible');
		var separatorCount = $(currentList).find('[' + plugin.selectors.separatorRow + ']').length + 1;
		var currentRole = $('[' + plugin.selectors.adminNavRoleSelect + ']').val();
		currentRole = currentRole.replace('menu_role_', '');
		
		var html = '<li class="np-nav-preview separator" data-np-separator-row>';
		html += '<div class="menu-item">';
		html += '<div class="submenu-toggle"></div>';
		html += '<div class="handle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class=" np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg></div>';
		html += '<div class="title"><div class="menu-icon dashicons-before dashicons-admin-post"></div><p>Separator<button class="button button-small details-button" data-np-remove-separator-button="">Remove</button></p></div><!-- .title -->';
		html += '</div><!-- .menu-item -->'
		html += '<input type="hidden" name="nestedpages_admin[nav_menu_options][' + currentRole + '][custom_sep' + separatorCount + ']" value="true">';
		html += '<input type="hidden" name="nestedpages_admin[nav_menu_options][' + currentRole + '][custom_sep' + separatorCount + '][order]" value="" data-np-menu-order>';
		html += '</li>';
		$(currentList).prepend(html);
		plugin.resetNavOrder($(currentList));
	}

	/**
	* Reset Orders
	*/
	plugin.resetNavOrder = function(list)
	{
		var orderFields = $(list).find('[data-np-menu-order]');
		$.each(orderFields, function(i, v){
			$(this).val(i);
		});
		plugin.enableSortableAdminSorting();
	}

	return plugin.bindEvents();
}

new NestedPages.AdminCustomizationSettings;