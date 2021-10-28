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
		global $np_menu_original;

		if ( !$this->settings->adminCustomEnabled('enabled_menu') ) return;
		$menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
		if ( !$menu_options ) return;
		if ( !isset($menu_options[$this->current_user_role]) ) return;

		$new_menu = array();
		$order = 1;
		$separator_index = 1;

		foreach ( $menu_options[$this->current_user_role] as $key => $item ){

			// Settings can't be hidden (unable to return to reset customizations)
			if ( isset($item['hidden']) && $item['link'] !== 'options-general.php' ) continue;

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


		// Append top level menu items added by plugins since customization
		foreach ( $np_menu_original[$this->current_user_role] as $key => $item) :
			$exists = false;
			foreach ( $new_menu as $new_menu_item ) :
				if ( $item[2] == $new_menu_item[2] ) $exists = true;;
			endforeach;
			$is_hidden = false;
			foreach ( $menu_options[$this->current_user_role] as $menu_option ) :
				if ( !isset($menu_option['link']) ) continue;
				if ( $item[2] == $menu_option['link'] && isset($menu_option['hidden']) ) $is_hidden = true;
			endforeach;
			if ( !$exists && $item[1] !== 'read' && !$is_hidden ) $new_menu[] = $item;
		endforeach;
		
		$menu = $new_menu;
	}

	/**
	* Build the Submenus
	*/
	public function createSubmenus()
	{
		global $submenu;
		global $np_submenu_original;
		$original_submenu = $np_submenu_original;

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

		// Add the Submenu pages back in, ordered and labeled how we want
		foreach ( $menu_options[$this->current_user_role] as $menu_option ){
		
			if ( !isset($menu_option['original_link']) ) continue;
			if ( !isset($menu_option['submenu']) || !$menu_option['submenu'] ){
				if ( !isset($np_submenu_original[$menu_option['original_link']]) ) continue;
				$submenu[$menu_option['link']] = $np_submenu_original[$menu_option['original_link']];
				continue;
			}
			$new_submenu = [];
			foreach ( $menu_option['submenu'] as $key => $menu ){
				$index = ($key + 1) * 10;
				
				// Items saved that no longer exist
				if ( !isset($np_submenu_original[$menu_option['link']]) ) continue;
				if ( !$this->submenuExists($np_submenu_original[$menu_option['link']], $menu['link'], $menu_option) ) continue;

				$np_submenu_original[$index][0] = $menu['label'];
				$np_submenu_original[$index][1] = $menu['role'];
				$np_submenu_original[$index][2] = $menu['link'];
				$np_submenu_original[$index][3] = $menu['label'];
				$np_submenu_original[$index][4] = (isset($menu['hidden']) && $menu['hidden'] == 'true') ? true : false;

				if ( isset($menu['hidden']) && $menu['hidden'] == 'true' ) continue;
				$new_submenu[$index][0] = $menu['label'];
				$new_submenu[$index][1] = $menu['role'];
				$new_submenu[$index][2] = $menu['link'];
				$new_submenu[$index][3] = $menu['label'];
			}
			$submenu[$menu_option['link']] = $new_submenu;
		}

		// Submenu pages added by plugins after saving customizations
		foreach ( $original_submenu as $id => $submenu_original ){
			foreach ( $submenu_original as $submenu_item ){
				if ( !isset($original_submenu[$id]) || empty($original_submenu[$id]) ) continue;
				if ( !isset($submenu[$id]) ) $submenu[$id] = [];
				if ( !isset($menu_options[$this->current_user_role][$id]) ) $menu_options[$this->current_user_role][$id] = [
					'submenu' => []
				];
				if ( isset($menu_options[$this->current_user_role][$id]) && !$this->submenuExists($submenu[$id], $submenu_item[2]) 
					&& !$this->submenuHidden($menu_options[$this->current_user_role][$id]['submenu'], $submenu_item[2]) ) {
						$submenu[$id][] = $submenu_item;
				}
			}
		}
	}

	/**
	* Does the submenu exist in the source menu?
	*/
	private function submenuExists($source_menu, $link)
	{
		$exists = false;
		foreach ( $source_menu as $menu_item ){
			if ( $menu_item[2] == $link ) $exists = true;
		}
		return $exists;
	}

	private function submenuHidden($menu_options, $submenu_link)
	{
		$hidden = false;
		foreach ( $menu_options as $option ){
			if ( $option['link'] == $submenu_link && isset($option['hidden']) ) $hidden = true;
		}
		return $hidden;
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
			$missing_submenus = [];
			foreach ( $menu_options[$role['name']] as $key => $item ){
				foreach ( $np_menu_original as $menu_key => $menu_item ){
					if ( isset($menu_item[2]) && $menu_item[2] == $key ) {
						if ( isset($np_submenu_original[$menu_item[2]]) ) {
							$menu_item['submenu'] = $np_submenu_original[$menu_item[2]];
						}
						$np_menu_ordered[$role['name']][] = $menu_item;
					} // submenu
				} // np menu original
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

		// Add missing items (added by other plugins after saving a custom menu)		
		$missing_top_items = [];
		$missing_sub_items = [];
		foreach ( $np_menu_ordered as $role => $ordered_menu ){
			if ( $role == 'default' ) continue;
			$all_items = [];
			foreach ( $np_menu_ordered['default'] as $menu_key => $default_menu ){
				if ( $default_menu[2] == 'edit-tags.php?taxonomy=link_category' ) continue;
				$all_items[$default_menu[2]] = $menu_key;
			}
			foreach ( $ordered_menu as $ordered_item ){
				if ( array_key_exists($ordered_item[2], $all_items) ) unset($all_items[$ordered_item[2]]);
			}
			$missing_top_items[$role] = $all_items;
		}		

		foreach ( $missing_top_items as $role => $items ){
			foreach ( $items as $item_id => $key ){
				$new_item = $np_menu_ordered['default'][$key];
				if ( array_key_exists($item_id, $np_submenu_original) ) :
					$new_item['submenu'] = $np_submenu_original[$item_id];
				endif;
				$np_menu_ordered[$role][] = $new_item;
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
		$user_roles = $this->user_repo->allRoles([]);

		foreach( $user_roles as $role ){

			$role_capabilities = $this->user_repo->getSingleRoleCapabilities($role['name']);

			foreach ( $np_menu_original as $menu_item ){
				
				if ( $menu_item[1] == 'list_users' && !array_key_exists('list_users', $role_capabilities) ){
					$profile_menu_item = [
						__('Profile', 'wp-nested-pages'),
						'read',
						'profile.php',
						'',
						'menu-icon-users',
						'menu-users',
						'dashicons-admin-users',
						'custom-item',
						'no-delete'
					];
					$np_menu_ordered[$role['name']][] = $profile_menu_item;
					continue;
				}

				if ( $menu_item[1] === '' || !array_key_exists($menu_item[1], $role_capabilities) || !$role_capabilities[$menu_item[1]] ) continue;
				if ( isset($menu_item[5]) && $menu_item[5] == 'menu-links' ) continue;
				if ( $role['name'] == 'subscriber' && $menu_item[2] == 'separator2') continue;
				if ( isset($np_submenu_original[$menu_item[2]]) ) $menu_item['submenu'] = $np_submenu_original[$menu_item[2]];
				$np_menu_ordered[$role['name']][] = $menu_item;
			}

		} // roles
		$np_menu_original = $np_menu_ordered;
	}
}