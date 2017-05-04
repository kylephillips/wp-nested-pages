<?php 

namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSyncMenu;
/**
* Hook into WP actions for necessary tasks related to nav menus
*/
class NavMenuActions 
{
	private $hookPriority = 10;

	public function __construct()
	{
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		if ( get_option('nestedpages_disable_menu') == 'true' ) return;
		$this->addUpdateHook();
	}

	private function addUpdateHook()
	{
		add_action( 'wp_update_nav_menu', array($this, 'syncMenu'), $this->hookPriority, 2 );
	}

	private function removeUpdateHook()
	{
		remove_action( 'wp_update_nav_menu', array($this, 'syncMenu'), $this->hookPriority);
	}

	/**
	* Sync Pages when updating nav menu
	*/
	public function syncMenu($menu_id, $menu_data = null)
	{
		$this->removeUpdateHook();
		if ( $menu_data == null ) $sync = new NavMenuSyncMenu($menu_id);
		$this->addUpdateHook();
	}

}