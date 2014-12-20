<?php namespace NestedPages\Controllers;

use NestedPages\Controllers\PageListingController;
use NestedPages\Helpers;

/**
* Admin Menus
* @since 1.2
*/
class AdminMenuController {

	/**
	* Page Post Type
	*/
	private $post_type;


	public function __construct()
	{
		add_action( 'admin_menu', array($this, 'adminMenu') );
	}


	/**
	* Add the admin menu items
	*/
	public function adminMenu()
	{
		$this->pageMenu();
	}

	
	/**
	* Page Menu
	* @since 1.2
	*/
	private function pageMenu()
	{
		$this->post_type = get_post_type_object('page');
		if ( (current_user_can('edit_pages')) || ($this->user->canSortPages()) ){
			add_menu_page( 
				__($this->post_type->labels->name),
				__($this->post_type->labels->name),
				'delete_pages',
				'nestedpages', 
				PageListingController::admin_menu(),
				'dashicons-admin-page',
				20
			);
			$this->pageSubmenu();
		}
	}


	/**
	* Page Submenus
	*/
	private function pageSubmenu()
	{
		global $submenu;
		$submenu['nestedpages'][50] = array( __('All Pages','nestedpages'), 'publish_pages', esc_url(admin_url('admin.php?page=nestedpages')) );
		$c = $this->submenu();
		// Default Pages
		if ( get_option('nestedpages_hidedefault') !== 'hide' ){
			$submenu['nestedpages'][$c] = array( __('Default Pages','nestedpages'), 'publish_pages', Helpers::defaultPagesLink() );
		}
	}


	/**
	* Add Submenus
	*/
	private function submenu()
	{
		global $submenu;
		
		// Get the right submenu and remove all pages link
		foreach($submenu as $key => $sub){
			if ($key == 'edit.php?post_type=' . $this->post_type->name){
				unset($sub['5']); // Remove "All Pages"
				$menu_items = $sub;
			}
		}
		if ( isset($menu_items) ){
			$c = 60;
			foreach($menu_items as $item){
				$submenu['nestedpages'][$c] = array( $item[0], $item[1], $item[2]);
				$c = $c + 10;
			}
		}
		return $c;
	}



}