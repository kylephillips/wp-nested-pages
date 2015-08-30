<?php 

namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* Base Nav Menu Sync class
*/
abstract class NavMenuSync 
{

	/**
	* Nav Menu Repository
	* @var object NavMenuRepository
	*/
	protected $nav_menu_repo;

	/**
	* The Menu ID
	* @var int
	*/
	protected $id;

	public function __construct()
	{
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		$this->nav_menu_repo = new NavMenuRepository;
		$this->setMenuID();
	}

	/**
	* Menu ID Setter
	*/
	protected function setMenuID()
	{
		$this->id = $this->nav_menu_repo->getMenuID();
	}

	/**
	* Remove a Menu Item
	* @since 1.3.4
	* @param int $id - ID of nav menu item
	*/
	protected function removeItem($id)
	{
		wp_delete_post($id, true);
	}

}