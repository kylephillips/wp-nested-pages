<?php
namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\AdminMenu\EnabledMenus;
use NestedPages\Entities\AdminMenu\AdminSubmenuExpander;

/**
* Admin Menus
* @since 1.2.1
*/
class AdminMenu 
{
	public function __construct()
	{
		add_action( 'admin_menu', array($this, 'setMenus') );
		add_action( 'admin_head', array($this, 'expandSubMenus') );
	}

	/**
	* Other Post Types
	*/
	public function setMenus()
	{
		new EnabledMenus;
	}

	/**
	* Expand Submenus
	*/
	public function expandSubMenus()
	{
		new AdminSubmenuExpander;
	}
}