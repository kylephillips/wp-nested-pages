<?php namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSync;
use NestedPages\Helpers;
use NestedPages\Entities\Post\PostUpdateRepository;

/**
* Syncs the Listing to Match the Menu
*/
class NavMenuSyncMenu extends NavMenuSync implements NavMenuSyncInterface {

	/**
	* Menu Items
	* @var array of objects
	*/
	private $menu_items;

	/**
	* Menu Index
	* @var array
	*/
	private $menu_index;

	/**
	* Post Repository
	* @var object
	*/
	private $post_update_repo;


	public function __construct()
	{
		parent::__construct();
		$this->post_update_repo = new PostUpdateRepository;
		$this->setMenuItems();
		$this->sync();
		return true;
	}


	/**
	* Get the menu items from menu and set them
	*/
	private function setMenuItems()
	{
		$this->menu_items = wp_get_nav_menu_items($this->id);
	}


	/**
	* Loop through the menu items and sync depending on type
	*/
	public function sync()
	{	
		$this->setMenuIndex();
		foreach($this->menu_items as $key => $item){
			$this->updatePost($item);
		}
	}

	/**
	* Set Menu Order/Parent Index
	*/
	private function setMenuIndex()
	{
		foreach($this->menu_items as $key => $item){
			$this->index[$item->ID] = array(
				'ID' => $item->object_id,
				'title' => $item->title
			);
		}
	}


	/**
	* Update the WP Post with Menu Data
	*/
	private function updatePost($item)
	{
		$parent_id = ( $item->menu_item_parent == '0' ) ? 0 : $this->index[$item->menu_item_parent]['ID'];
		
		if ( $this->post_update_repo->isNavMenuItem($parent_id) ) {
			$parent_id = $this->post_update_repo->getLinkfromTitle($this->index[$item->menu_item_parent]['title']);
		}

		$post_data = array(
			'menu_order' => $item->menu_order,
			'post_id' => $item->object_id,
			'link_target' => $item->target,
			'np_nav_title' => $item->title,
			'np_title_attribute' => $item->attr_title,
			'post_parent' => $parent_id,
			'np_nav_css_classes' => $item->classes
		);
		if ( $item->type == 'custom' ) $post_data['content'] = $item->url;
		$this->post_update_repo->updateFromMenuItem($post_data);
	}

}
