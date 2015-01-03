<?php namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;
use NestedPages\Helpers;

/**
* Syncs the Generated Menu to Match the Listing
*/
class NavMenuListingSync {

	/**
	* Nav Menu Repository
	* @var object NavMenuRepository
	*/
	private $nav_menu_repo;

	/**
	* The Menu ID
	* @var int
	*/
	private $id;

	/**
	* Individual Post
	* @var array
	*/
	private $post;


	public function __construct()
	{
		$this->nav_menu_repo = new NavMenuRepository;
		$this->setMenuID();
		$this->nav_menu_repo->clearMenu($this->id);
	}


	/**
	* Menu ID Setter
	*/
	private function setMenuID()
	{
		$this->id = $this->nav_menu_repo->getMenuID();
	}


	/**
	* Set the post settings
	* @param object - post object
	* @since 1.1.4
	*/
	private function set_post($post)
	{
		$this->post['ID'] = $post->ID;
		$this->post['show_in_nav'] = get_post_meta( $post->ID, 'np_nav_status', true);
		$this->post['nested_pages_visible'] = get_post_meta( $post->ID, 'nested_pages_status', true );
		$this->post['link_target'] = get_post_meta( $post->ID, 'np_link_target', true );
		$this->post['title_attribute'] = get_post_meta( $post->ID, 'np_title_attribute', true );
		$this->post['css_classes'] = get_post_meta( $post->ID, 'np_nav_css_classes', true );
		$this->post['permalink'] = get_the_permalink($post->ID);

		$nav_title = get_post_meta( $post->ID, 'np_nav_title', true );
		$this->post['nav_title'] = ( $nav_title !== "" ) ? $nav_title : $post->post_title;
	}


	/**
	* Create the menu with nested pages (Recursive function)
	*/
	public function sync($parent = 0, $menu_parent = 0)
	{
		$page_q = new \WP_Query(array(
			'post_type' => array('page','np-redirect'),
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_parent' => $parent
		));
		if ( $page_q->have_posts() ) : while ( $page_q->have_posts() ) : $page_q->the_post();
			global $post;
			$this->set_post($post);
			if ( ($this->post['show_in_nav'] == 'show') || ($this->post['show_in_nav'] == '') ) {
				$menu = ( get_post_type() == 'page' ) ? $this->syncPageItem($menu_parent) : $this->syncLinkItem($menu_parent);
				$this->sync( get_the_id(), $menu );
			}
		endwhile; endif; wp_reset_postdata();
	}


	/**
	* Sync Page Menu Item
	* @since 1.1.4
	*/
	private function syncPageItem($menu_parent)
	{
		$menu = wp_update_nav_menu_item($this->id, 0, array(
			'menu-item-title' => $this->post['nav_title'],
			'menu-item-url' => $this->post['permalink'],
			'menu-item-attr-title' => $this->post['title_attribute'],
			'menu-item-status' => 'publish',
			'menu-item-classes' => $this->post['css_classes'],
			'menu-item-type' => 'post_type',
			'menu-item-object' => 'page',
			'menu-item-object-id' => $this->post['ID'],
			'menu-item-parent-id' => $menu_parent,
			'menu-item-target' => $this->post['link_target']
		));
		return $menu;
	}


	/**
	* Sync Link Menu Item
	* @since 1.1.4
	*/
	private function syncLinkItem($menu_parent)
	{
		$menu = wp_update_nav_menu_item($this->id, 0, array(
			'menu-item-title' => $this->post['nav_title'],
			'menu-item-url' => Helpers::check_url(get_the_content($this->post['ID'])),
			'menu-item-attr-title' => $this->post['title_attribute'],
			'menu-item-status' => 'publish',
			'menu-item-classes' => $this->post['css_classes'],
			'menu-item-type' => 'custom',
			'menu-item-object' => 'page',
			'menu-item-object-id' => $this->post['ID'],
			'menu-item-parent-id' => $menu_parent,
			'menu-item-target' => $this->post['link_target']
		));
		return $menu;
	}


}