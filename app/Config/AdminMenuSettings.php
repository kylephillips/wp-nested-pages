<?php
namespace NestedPages\Config;

use NestedPages\Entities\User\UserRepository;
use NestedPages\Config\SettingsRepository;

/**
* Displays the admin menu settings
*/
class AdminMenuSettings
{
	private $user_repo;

	private $settings;

	public $roles;

	public $nav_menu_options;

	public $np_menu_original;

	public $np_submenu_original;

	public $menu;

	public function __construct()
	{
		$this->user_repo = new UserRepository;
		$this->settings = new SettingsRepository;
		$this->roles = $this->user_repo->allRoles(null, true); 
		$this->nav_menu_options = $this->settings->adminCustomEnabled('nav_menu_options');

		global $np_menu_original;
		$this->np_menu_original = $np_menu_original;
		global $np_submenu_original;
		$this->np_submenu_original = $np_submenu_original;
		global $menu;
		$this->menu = $menu;
	}

	/**
	* Data for an individual menu item
	*/
	public function menuItemData($menu_item, $role)
	{
		$data = [];
		$id = $menu_item[2];
		$data['id'] = $id;

		// Separator/WooCommerce Separator
		$data['separator'] = ( $menu_item[0] == '' && isset($menu_item[4]) && $menu_item[4] == 'wp-menu-separator' ) ? true : false;
		if ( isset($menu_item[4]) && $menu_item[4] == 'wp-menu-separator woocommerce' ) $data['separator'] = true;
		
		// Set the Label
		$text = strip_tags(preg_replace('#<span[^>]*>(.*)</span>#isU','', $menu_item[0]));
		$original_text = $text;
		$data['custom_label'] = null;
		if ( isset($this->nav_menu_options[$role['name']][$id]['label']) && $this->nav_menu_options[$role['name']][$id]['label'] !== '' )
			$data['custom_label'] = $this->nav_menu_options[$role['name']][$id]['label'];
		if ( $data['separator'] ) $custom_label = __('Separator', 'wp-nested-pages');
		if ( $data['custom_label'] ) $text = $data['custom_label'];
		$data['text'] = $text;
		$data['original_text'] = $original_text;

		// Set the Icon
		$icon = ( isset($menu_item[6]) && $menu_item[6] !== '' ) ? $menu_item[6] : 'dashicons-admin-post';
		$data['original_icon'] = $icon;
		$data['custom_icon'] = null;
		if ( isset($this->nav_menu_options[$role['name']][$id]['icon']) && $this->nav_menu_options[$role['name']][$id]['icon'] !== '' )
			$data['custom_icon'] = $this->nav_menu_options[$role['name']][$id]['icon'];
		if ( $data['custom_icon'] ) $icon = $data['custom_icon'];
		$data['icon'] = $icon;

		// Set the original link
		$data['original_link'] = $menu_item[2];

		// Set the submenu
		$data['submenu'] = ( isset($menu_item['submenu']) ) ? $menu_item['submenu'] : null;
		$data['has_custom_submenu'] = ( isset($this->nav_menu_options[$role['name']][$id]['submenu']) ) ? true : false;
		$data['submenu_items'] = $this->submenuItems($data, $role);

		return $data;
	}

	/**
	* Submenu Items
	*/
	public function submenuItems($menu_item, $role)
	{
		// Get all submenus
		if ( !array_key_exists($role['name'], $this->np_menu_original) ) return $menu_item;
		$unordered_menu = $this->np_menu_original[$role['name']];
		$unordered_submenus = [];
		foreach ( $unordered_menu as $unordered_menu_item ){
			if ( isset($unordered_menu_item['submenu']) ) {
				$unordered_submenus[$unordered_menu_item[2]] = $unordered_menu_item['submenu'];
			}
		}

		$id = $menu_item['id'];

		// Use the custom menu if available, otherwise use default
		$submenu_items = ( $menu_item['has_custom_submenu'] ) 
			? $this->nav_menu_options[$role['name']][$id]['submenu']
			: $menu_item['submenu'];

		if ( !$menu_item['has_custom_submenu'] ) return $submenu_items;

		// Remove submenu items not active (removed by plugin deactivation since last save)
		foreach ( $submenu_items as $submenu_key => $submenu_item ) :
			$exists = false;
			foreach ( $unordered_submenus[$id] as $unordered_item ) :
				if ( $unordered_item[2] == $submenu_item['link'] ) $exists = true;
			endforeach;
			if ( !$exists ) unset($submenu_items[$submenu_key]);
		endforeach;

		// Add any missing submenu items (added by plugins since last save)
		$missing_items = [];
		foreach ( $unordered_submenus[$id] as $unordered_item ) :
			$missing = true;
			foreach ( $submenu_items as $submenu_key => $submenu_item ) :
				if ( $submenu_item['link'] == $unordered_item[2] ) $missing = false;
			endforeach;
			if ( $missing ){
				$submenu_items[] = [
					'label' => $unordered_item[0],
					'role' => $unordered_item[1],
					'link' => $unordered_item[2],
					'order' => count($submenu_items) + 1
				];
			}
		endforeach;
		return $submenu_items;
	}

}