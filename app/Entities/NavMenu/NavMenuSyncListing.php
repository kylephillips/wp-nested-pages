<?php
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuSync;
use NestedPages\Helpers;
use NestedPages\Entities\Post\PostDataFactory;

/**
* Syncs the Generated Menu to Match the Listing
*/
class NavMenuSyncListing extends NavMenuSync 
{
	/**
	* Individual Post
	* @var array
	*/
	private $post;

	/**
	* Menu Position Count
	* @var int
	*/
	private $count = 0;

	/**
	* Private Pages Sync
	* @var bool
	*/
	private $private_enabled = false;

	/**
	* Post Data Factory
	*/
	private $post_factory;

	public function __construct()
	{
		parent::__construct();
		$this->private_enabled = $this->settings->privateMenuEnabled();
		$this->post_factory = new PostDataFactory;
	}

	/**
	* Recursive function loops through pages/links and their children
	*/
	public function sync($parent = 0, $menu_parent = 0, $nest_level = 0)
	{	
		$post_types = ['page'];
		if ( !$this->integrations->plugins->wpml->installed ) $post_types[] = 'np-redirect';
		try {
			$this->count = $this->count + 1;
			$args = [
				'post_type' => $post_types,
				'posts_per_page' => -1,
				'post_status' => ['publish', 'pending', 'draft', 'private', 'future', 'trash'],
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'post_parent' => $parent
			];
			$page_q = new \WP_Query(apply_filters('nestedpages_menu_sync', $args, $nest_level));
			if ( $page_q->have_posts() ) : while ( $page_q->have_posts() ) : $page_q->the_post();
				$nest_level++;
				global $post;
				$this->post = $this->post_factory->build($post);
				$this->syncPost($menu_parent, $nest_level);
			endwhile; endif; wp_reset_postdata();
		} catch ( \Exception $e ){
			throw new \Exception($e->getMessage());
		}
	}

	/**
	* Sync an individual item
	* @since 1.3.4
	*/
	private function syncPost($menu_parent, $nest_level)
	{
		// Get the Menu Item
		$query_type = ( $this->post->type == 'np-redirect' ) ? 'xfn' : 'object_id';
		$menu_item_id = $this->nav_menu_repo->getMenuItem($this->post->id, $query_type);
		if ( $this->post->nav_status == 'hide' ) return $this->removeItem($menu_item_id);
		if ( $this->post->post_status !== 'publish' && !$this->private_enabled ) return $this->removeItem($menu_item_id);
		$menu = $this->syncMenuItem($menu_parent, $menu_item_id);
		$this->sync( $this->post->id, $menu, $nest_level );
	}

	/**
	* Sync Link Menu Item
	* @since 1.1.4
	*/
	private function syncMenuItem($menu_parent, $menu_item_id)
	{
		$type = ( $this->post->nav_type ) ? $this->post->nav_type : 'custom';
		$object = ( $this->post->nav_object ) ? $this->post->nav_object : 'custom';
		$object_id = ( $this->post->nav_object_id  ) ? intval($this->post->nav_object_id) : null;
		$url = ( $type == 'custom' ) ? esc_url($this->post->content) : '';
		$xfn = $this->post->id;
		$title = ( $this->post->nav_title && $this->post->type == 'page' ) ? $this->post->nav_title : $this->post->title;
		
		// Compatibility for 1.4.1 - Reset Page links
		if ( $this->post->type == 'page' ){
			$type = 'post_type';
			$object = 'page';
			$object_id = $this->post->id;
			$xfn = 'page';
		}

		// WP 4.4 Fix, empty nav title attribute causing post_excerpt null error
		$attr_title = ( $this->post->nav_title_attr ) ? $this->post->nav_title_attr : '';

		$args = [
			'menu-item-title' => $title,
			'menu-item-position' => $this->count,
			'menu-item-url' => $url,
			'menu-item-attr-title' => $attr_title,
			'menu-item-status' => 'publish',
			'menu-item-classes' => $this->post->nav_css,
			'menu-item-type' => $type,
			'menu-item-object' => $object,
			'menu-item-object-id' => $object_id,
			'menu-item-parent-id' => $menu_parent,
			'menu-item-xfn' => $xfn,
			'menu-item-target' => $this->post->link_target,
			'menu-item-description' => ' '
		];
		$menu = wp_update_nav_menu_item($this->id, $menu_item_id, $args);
		return $menu;
	}
}