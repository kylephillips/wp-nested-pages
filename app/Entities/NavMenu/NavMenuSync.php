<?php namespace NestedPages\Entities\NavMenu;

/**
* Base Nav Menu Sync class
*/
abstract class NavMenuSync {

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
		$this->nav_menu_repo = new NavMenuRepository;
		$this->setMenuID();
		$this->nav_menu_repo->clearMenu($this->id);
	}


	/**
	* Menu ID Setter
	*/
	protected function setMenuID()
	{
		$this->id = $this->nav_menu_repo->getMenuID();
	}


}