<?php 

namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSyncMenu;
/**
* Hook into WP actions for necessary tasks related to nav menus
*/
class NavMenuActions 
{

	public function __construct()
	{
		add_action( 'wp_update_nav_menu', array($this, 'syncMenu'), 10 ,2 );
	}

	/**
	* Sync Pages when updating nav menu
	*/
	public function syncMenu($menu_id, $menu_data = null)
	{
		// Core calls action twice. Only want it to run once. 
		// Don't need it to run in wp_update_nav_menu_object function
		if ( $menu_data == null ) $sync = new NavMenuSyncMenu($menu_id);
	}

}