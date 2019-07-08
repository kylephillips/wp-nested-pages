<?php 
namespace NestedPages\Entities\AdminCustomization;

/**
* Custom Admin Menu Items
*/
class AdminMenuItems extends AdminCustomizationBase
{
	public function __construct()
	{
		parent::__construct();
		add_action('admin_menu', [$this, 'setOriginalMenus']);
		add_action('admin_menu', [$this, 'createCustomMenu']);
		add_action('admin_menu', [$this, 'relabelItems']);
		add_action('admin_menu', [$this, 'createSubmenus']);
	}

	/**
	* Save the original menu before it is modified (administrators)
	*/
	public function setOriginalMenus()
	{
		global $menu;
		global $submenu;
		global $np_menu_original;
		global $np_submenu_original;
		$np_menu_original = $menu;
		$np_submenu_original = $submenu;
		$this->buildNestedMenu();
	}

	/**
	* Relabel Items
	*/
	public function relabelItems()
	{
		if ( !$this->settings->adminCustomEnabled('enabled_menu') ) return;
		$menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
		if ( empty($menu_options) ) return;
		
		global $menu;
		
		// Label
		foreach ( $menu as $key => $item ){
			if ( isset($menu_options[$this->current_user_role][$item[2]]) && isset($menu_options[$this->current_user_role][$item[2]]['label']) ){
				if ( $menu_options[$this->current_user_role][$item[2]]['label'] == '' ) continue;

				$custom_label = sanitize_text_field($menu_options[$this->current_user_role][$item[2]]['label']);
				// Add notification counts if needed
				$dom = new \DOMDocument;
				$dom->loadHtml($item[0]);
				$span_tags = $dom->getElementsByTagName('span');
				if ( $span_tags->length > 0 ) $custom_label .= "<span class='update-plugins count-{$span_tags[0]->nodeValue}'><span class='plugin-count'>{$span_tags[0]->nodeValue}</span></span>";
				$menu[$key][0] = $custom_label;
			}
		}

		// Icon
		foreach ( $menu as $key => $item ){
			if ( !isset($item[6]) ) continue;
			if ( isset($menu_options[$this->current_user_role][$item[2]]) && isset($menu_options[$this->current_user_role][$item[2]]['icon']) ){
				if ( $menu_options[$this->current_user_role][$item[2]]['icon'] == '' ) continue;
				$menu[$key][6] = sanitize_text_field($menu_options[$this->current_user_role][$item[2]]['icon']);
			}
		}

		// Link
		foreach ( $menu as $key => $item ){
			if ( !isset($item[2]) ) continue;
			if ( isset($menu_options[$this->current_user_role][$item[2]]) && isset($menu_options[$this->current_user_role][$item[2]]['link']) ){
				if ( $menu_options[$this->current_user_role][$item[2]]['link'] == '' ) continue;
				$menu[$key][2] = sanitize_text_field($menu_options[$this->current_user_role][$item[2]]['link']);
			}
		}
	}

	/**
	* Reorder Menu Items 
	* (Can't hook into the menu_order filter because it receives links. Links may change in plugin settings)
	*/
	public function createCustomMenu()
	{
		$this->setCurrentUserRoles();
		global $menu;
		if ( !$this->settings->adminCustomEnabled('enabled_menu') ) return;
		$menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
		if ( !$menu_options ) return;
		if ( !isset($menu_options[$this->current_user_role]) ) return;

		$new_menu = array();
		$order = 1;
		$separator_index = 1;
		foreach ( $menu_options[$this->current_user_role] as $key => $item ){

			if ( isset($item['hidden']) ) continue;
			$slug = ( isset($item['original_link']) ) ? $item['original_link'] : $key;

			// Loop through the original menu and get the item if it exists (associated through original link)
			foreach ( $menu as $original_item ){
				if ( isset($original_item[2]) && $original_item[2] == $slug ){
					$new_menu[$order] = $original_item;
				}
			}

			// Add Separators
			if ( strpos($key, 'custom_sep') !== false || strpos($key, 'separator') !== false ) {
				$new_menu[$order] = array('', 'read', 'separator' . $separator_index, '', 'wp-menu-separator');
				$separator_index++;
			}

			$order++;
		}
		$menu = $new_menu;
	}

	/**
	* Build the Submenus
	*/
	public function createSubmenus()
	{
		global $submenu;
		global $np_submenu_original;

		if ( !$this->settings->adminCustomEnabled('enabled_menu') ) return;
		$menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
		if ( !$menu_options ) return;
		if ( !isset($menu_options[$this->current_user_role]) ) return;

		// First remove all submenus
		foreach ( $submenu as $menu_slug => $submenu_items ){
			foreach ( $submenu_items as $sub_pages ){
				if ( !isset($sub_pages[2]) ) continue;
				remove_submenu_page($menu_slug, $sub_pages[2]);
			}
		}

		// var_dump($submenu);

		// Add the Submenu pages back in, ordered and labeled how we want
		foreach ( $menu_options[$this->current_user_role] as $menu_option ){
			// var_dump($menu_option);
			if ( !isset($menu_option['original_link']) ) continue;
			// var_dump($np_submenu_original[$menu_option['original_link']]);
			$submenu[$menu_option['link']] = $np_submenu_original[$menu_option['original_link']];
		}
	}

	/**
	* Reorder Menu Items in the original, saved copy
	*/
	public function buildNestedMenu()
	{
		global $np_menu_original;
		global $np_submenu_original;
		global $menu;

		$menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
		if ( !$menu_options ) return $this->setDefaultMenu();
		
		$np_menu_ordered = array();		
		$np_menu_ordered['default'] = $menu;

		// Set each role's menu order
		$user_roles = $this->user_repo->allRoles(array());
		foreach( $user_roles as $role ){
			if ( !array_key_exists($role['name'], $menu_options) ) continue;

			foreach ( $menu_options[$role['name']] as $key => $item ){

				foreach ( $np_menu_original as $menu_key => $menu_item ){
					if ( isset($menu_item[2]) && $menu_item[2] == $key ) {
						if ( isset($np_submenu_original[$menu_item[2]]) ) $menu_item['submenu'] = $np_submenu_original[$menu_item[2]];
						$np_menu_ordered[$role['name']][] = $menu_item;
					} // submenu
				} // np menu original

				// Add the custom menu items
				if ( isset($item['custom']) ){
					$order = intval($item['order']);
					$label = ( isset($item['label']) ) ? sanitize_text_field($item['label']) : __('Untitled', 'wp-nested-pages');
					if ( $label == '' ) $label = __('Untitled', 'wp-nested-pages');
					$id = strtolower(str_replace(' ', '-', $label));
					$icon = ( isset($item['icon']) ) ? sanitize_text_field($item['icon']) : __('dashicons-admin-post', 'wp-nested-pages');
					$link = $key;
					$no_delete = array('profile.php');
					$custom_menu_item = array(
						$label, 'read', $link, '', 'menu-top', $id, $icon, 'custom-item'
					);
					if ( in_array($key, $no_delete) ) $custom_menu_item[] = 'no-delete';
					$np_menu_ordered[$role['name']][$order] = $custom_menu_item;
				}
			} // role
		}

		// Add the separators
		foreach($user_roles as $role){
			$role_name = $role['name'];
			if ( !array_key_exists($role_name, $menu_options) ) continue;
			$c = 0;
			foreach ( $menu_options[$role_name] as $key => $options ){
				if ( strpos($key, 'custom_sep') !== false ){
					$menu_item = array('', 'read', $key, '', 'wp-menu-separator');
					array_splice($np_menu_ordered[$role_name], $c, 0, array($menu_item));
				}
				$c++;
			}
		}

		$np_menu_original = $np_menu_ordered;
	}

	/**
	* Set the default menu/order
	* This is used to display the nested admin settings view before a customized version has been saved
	*/
	private function setDefaultMenu()
	{
		global $np_menu_original;
		global $np_submenu_original;
		$np_menu_ordered = array();
		ksort($np_menu_original);

		// Default menu (for new user roles)
		foreach ( $np_menu_original as $menu_item ){
			$np_menu_ordered['default'][] = $menu_item;
		}
		
		// Set each role's menu order
		$user_roles = $this->user_repo->allRoles(array());
		foreach( $user_roles as $role ){

			$role_capabilities = $this->user_repo->getSingleRole($role['name']);
			$role_capabilities = $role_capabilities['capabilities'];

			foreach ( $np_menu_original as $menu_item ){
				
				if ( $menu_item[1] == 'list_users' && !array_key_exists('list_users', $role_capabilities) ){
					$profile_menu_item = array(
						__('Profile', 'wp-nested-pages'),
						'read',
						'profile.php',
						'',
						'menu-icon-users',
						'menu-users',
						'dashicons-admin-users',
						'custom-item',
						'no-delete'
					);
					$np_menu_ordered[$role['name']][] = $profile_menu_item;
					continue;
				}

				if ( !array_key_exists($menu_item[1], $role_capabilities) || !$role_capabilities[$menu_item[1]] ) continue;
				if ( isset($menu_item[5]) && $menu_item[5] == 'menu-links' ) continue;
				if ( $role['name'] == 'subscriber' && $menu_item[2] == 'separator2') continue;
				if ( isset($np_submenu_original[$menu_item[2]]) ) $menu_item['submenu'] = $np_submenu_original[$menu_item[2]];
				$np_menu_ordered[$role['name']][] = $menu_item;
			}

		} // roles
		$np_menu_original = $np_menu_ordered;
	}
}