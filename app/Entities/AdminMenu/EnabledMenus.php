<?php
namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\Listing\Listing;
use NestedPages\Entities\AdminMenu\AdminSubmenu;
use NestedPages\Entities\AdminMenu\AdminSubmenuDefault;
use NestedPages\Entities\User\UserRepository;

/**
* Other User-Enabled Post Types
*/
class EnabledMenus 
{
	/**
	* Post Type
	*/
	private $post_type;

	/**
	* Post Type Repository
	* @var object
	*/
	private $post_type_repo;

	/**
	* Enabled Post Types
	*/
	private $enabled_types;

	/**
	* User Repository
	*/
	private $user;

	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->user = new UserRepository;
		$this->setEnabled();
		$this->loopEnabledTypes();
	}

	/**
	* Set Enabled Post Types
	*/
	private function setEnabled()
	{
		$this->enabled_types = $this->post_type_repo->getPostTypesObject();
	}

	/**
	* Set the Menus for each of the enabled post types
	*/
	private function loopEnabledTypes()
	{
		$c = 1; // Counter for position
		global $np_page_params;
		foreach($this->enabled_types as $key => $type){	
			if ( $type->np_enabled !== true ) continue;
			if ( $type->replace_menu ) {
				$this->post_type = get_post_type_object($key);
				if ( (current_user_can($this->post_type->cap->edit_posts)) || ($this->user->canSortPages()) ){
					$this->addMenu($c);
					$this->addSubmenu();
					$this->removeExistingMenu();
				}
			} else {
				$default = new AdminSubmenuDefault($type);
				$np_page_params[$default->getHook()] = array('post_type' => $type->name);
			}
			$c++;
		}
	}

	/**
	* Add the primary top-level menu item
	* @param int counter
	*/
	private function addMenu($c)
	{
		global $np_page_params;
		$hook = add_menu_page( 
			__($this->post_type->labels->name),
			__($this->post_type->labels->name),
			$this->post_type->cap->edit_posts,
			$this->getSlug(), 
			Listing::admin_menu($this->post_type->name),
			$this->menuIcon(),
			$this->menuPosition($c)
		);
		$np_page_params[$hook] = array('post_type' => $this->post_type->name);
	}

	/**
	* Add Submenus
	*/
	private function addSubmenu()
	{
		$submenu = new AdminSubmenu($this->post_type);
		$submenu->addSubmenu();
	}

	/**
	* Remove Default Menus
	*/
	private function removeExistingMenu()
	{
		remove_menu_page('edit.php?post_type=' . $this->post_type->name);
		if ( $this->post_type->name == 'post' ) remove_menu_page('edit.php');
	}

	/**
	* Get the correct icon to use in menu
	* @return string
	*/
	private function menuIcon()
	{
		if ( $this->post_type->name == 'page' )	return 'dashicons-admin-page';
		if ( $this->post_type->menu_icon ) return $this->post_type->menu_icon;
		return 'dashicons-admin-post';
	}

	/**
	* Get the correct menu position for item
	* @param int counter
	*/
	private function menuPosition($c)
	{
		global $_wp_last_object_menu;
		if ( $this->post_type->name == 'post' ) return 5;
		if ( $this->post_type->name == 'page') return 20;
		if ( $this->post_type->menu_position ) return $this->post_type->menu_position + 1;
		return $_wp_last_object_menu + $c;
	}

	/**
	* Get the Edit Slug for post type
	*/
	private function getSlug()
	{
		return $this->post_type_repo->getMenuSlug($this->post_type);
	}
}