<?php
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSync;
use NestedPages\Helpers;
use NestedPages\Entities\Post\PostUpdateRepository;
use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* Syncs the Listing to Match the Menu
*/
class NavMenuSyncMenu extends NavMenuSync 
{
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
	* Post Update Repository
	* @var object
	*/
	private $post_update_repo;

	/**
	* Post Repository
	* @var object
	*/
	private $post_repo;

	public function __construct()
	{
		parent::__construct();
		$this->post_update_repo = new PostUpdateRepository;
		$this->post_repo = new PostRepository;
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
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		$this->setMenuIndex();
		$this->updatePagesNavStatus();
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
			$this->index[$item->ID] = [
				'ID' => $item->object_id,
				'title' => $item->title
			];
		}
	}

	/**
	* Update the WP Post with Menu Data
	*/
	private function updatePost($item)
	{
		$parent_id = ( $item->menu_item_parent == '0' || !isset($this->index[$item->menu_item_parent]['ID']) ) ? 0 : $this->index[$item->menu_item_parent]['ID'];
		
		if ( $this->nav_menu_repo->isNavMenuItem($parent_id) ) {
			$parent_id = $this->nav_menu_repo->getLinkfromTitle($this->index[$item->menu_item_parent]['title']);
		}

		$post_id = ( $item->xfn !== 'page' )
			? $item->xfn
			: $item->object_id;
		
		$post_data = [
			'menu_order' => $item->menu_order,
			'post_id' => $post_id,
			'link_target' => $item->target,
			'np_nav_title' => $item->title,
			'np_title_attribute' => $item->attr_title,
			'post_parent' => $parent_id,
			'np_nav_css_classes' => $item->classes
		];
		if ( $item->type == 'custom' ) {
			$post_data['content'] = $item->url;
			$post_data['post_id'] = $item->xfn;
		}
		
		if ( is_string(get_post_status($post_id)) ){
			$this->post_update_repo->updateFromMenuItem($post_data);
		} else {
			$this->syncNewLink($item, $parent_id);
		}
	}

	/**
	* Sync a new Link
	*/
	private function syncNewLink($item, $parent_id)
	{
		if ( $this->integrations->plugins->wpml->installed ) return;
		$post_data = [
			'menuTitle' => $item->title,
			'np_link_title' => $item->title,
			'_status' => 'publish',
			'np_link_content' => $item->url,
			'parent_id' => $parent_id,
			'post_type' => 'np-redirect',
			'link_target' => $item->target,
			'menu_order'=> $item->menu_order,
			'menuType' => $item->type,
			'objectType' => $item->object,
			'objectId' => $item->object_id,
			'titleAttribute' => $item->attr_title
		];
		$post_id = $this->post_update_repo->saveRedirect($post_data);
		update_post_meta($item->ID, '_menu_item_xfn', absint($post_id));
	}

	/**
	* Update NP Nav Status for Pages
	* If a menu item is removed, it's nav status needs to be set to hide
	*/
	private function updatePagesNavStatus()
	{
		$visible_pages = $this->nav_menu_repo->getPagesInMenu();
		foreach($this->index as $key => $item){
			$menu_items[$key] = $item['ID'];
		}
		foreach($visible_pages as $page){
			if ( !in_array($page, $menu_items) ) {
				$data['post_id'] = $page;
				$data['nav_status'] = 'hide';
				$this->post_update_repo->updateNavStatus($data);
			}
		}
	}
}