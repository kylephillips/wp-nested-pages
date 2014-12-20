<?php namespace NestedPages\Entities;

use NestedPages\Helpers;

/**
* The generated nav menu that matches the nested pages structure
*/
class NavMenu {

	/**
	* The Menu ID
	*/
	public $id;

	/**
	* The Menu Items
	*/
	public $items;

	/**
	* Menu Object
	* @var object
	*/
	private $menu;

	/**
	* Individual Post
	* @var array
	*/
	private $post;


	public function __construct()
	{
		$this->setMenu();
		$this->setID();
		$this->setItems();
	}


	/**
	* Get Optional custom menu name
	* @since 1.1.5
	*/
	private function setMenu()
	{
		if ( get_option('nestedpages_menu') ){
			$menu_id = get_option('nestedpages_menu');
			$this->menu = get_term_by('id', $menu_id, 'nav_menu');
		} else {
			$this->createNewMenu();
			$this->setMenu();
		}
	}


	/**
	* Create Empty Menu if one doesn't exist
	*/
	private function createNewMenu()
	{
		$menu_id = wp_create_nav_menu('Nested Pages');
		update_option('nestedpages_menu', $menu_id);
	}


	/**
	* Set the Menu ID
	*/
	public function setID()
	{
		$this->id = $this->menu->term_id;
	}


	/**
	* Set the Menu Items
	*/
	public function setItems()
	{
		$menu = get_term_by('id', $this->id, 'nav_menu');
		if ( $menu ) {
			$this->items = wp_get_nav_menu_items($this->id);
		} else {
			$this->createNewMenu();
			$this->setItems();
		}
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
	}


	/**
	* Clear out the menu
	*/
	public function clearMenu()
	{
		$menu_items = wp_get_nav_menu_items($this->id);
		foreach ( $menu_items as $i ){
			wp_delete_post($i->ID, true);
		}
	}


}