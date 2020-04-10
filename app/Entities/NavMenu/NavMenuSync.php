<?php 
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;
use NestedPages\Config\SettingsRepository;

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

	/**
	* Settings Repository
	* @var object
	*/
	protected $settings;

	/**
	* Plugin Integrations
	* @var object
	*/
	protected $integrations;

	public function __construct()
	{
		$this->nav_menu_repo = new NavMenuRepository;
		$this->integrations = new IntegrationFactory;
		$this->settings = new SettingsRepository;
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