<?php 
// Replace app/Entities/NavMenu/NavMenuActions.php with this:

namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSyncMenu;
use NestedPages\Config\SettingsRepository;
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
		$settings = new SettingsRepository;
		$nested_pages_menu_id = get_option('nestedpages_menu');
		// Core calls action twice. Only want it to run once. 
		// Don't need it to run in wp_update_nav_menu_object function
		if ( !$settings->menusDisabled() && $menu_id == $nested_pages_menu_id && $menu_data == null ){
			$sync = new NavMenuSyncMenu($menu_id);
		}
	}

}