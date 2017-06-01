<?php 
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSyncMenu;
use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* Hook into WP actions for necessary tasks related to nav menus
*/
class NavMenuActions 
{
	/**
	* Nav Menu Repository
	*/
	private $nav_menu_repo;

	public function __construct()
	{
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		if ( get_option('nestedpages_disable_menu') == 'true' ) return;
		$this->nav_menu_repo = new NavMenuRepository;
		$this->addUpdateHook();
	}

	private function addUpdateHook()
	{
		add_action( 'wp_update_nav_menu', array($this, 'syncMenu'), 10, 2 );
	}

	private function removeUpdateHook()
	{
		remove_action( 'wp_update_nav_menu', array($this, 'syncMenu'), 10);
	}

	/**
	* Sync Pages when updating nav menu
	*/
	public function syncMenu($menu_id, $menu_data = null)
	{
		if ( $menu_id !== $this->nav_menu_repo->getMenuID() ) return; // Don't try to sync menus not managed by NP
		$this->removeUpdateHook();
		if ( $menu_data == null ) $sync = new NavMenuSyncMenu($menu_id);
		$this->addUpdateHook();
	}
}