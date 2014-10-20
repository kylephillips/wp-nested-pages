<?php
/**
* The generated nav menu that matches the nested pages structure
*/
class NP_NavMenu {

	/**
	* The Menu ID
	*/
	public $id;

	/**
	* The Menu Items
	*/
	public $items;


	public function __construct()
	{
		$this->setID();
		$this->setItems();
	}


	/**
	* Add the Nav Menu
	*/
	public function addMenu()
	{
		$menu = wp_create_nav_menu('nestedpages');
		$this->id = $menu;
	}


	/**
	* Set the Menu ID
	*/
	public function setID()
	{
		$menu = get_term_by('slug', 'nestedpages', 'nav_menu');
		if ( $menu ) $this->id = $menu->term_id;
	}


	/**
	* Set the Menu Items
	*/
	public function setItems()
	{
		$menu = get_term_by('slug', 'nestedpages', 'nav_menu');
		if ( $menu ) $this->items = wp_get_nav_menu_items($this->id);
	}


	/**
	* Create the menu with nested pages (Recursive function)
	*/
	public function sync($parent = 0, $menu_parent = 0)
	{
		$page_q = new WP_Query(array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_parent' => $parent
		));
		if ( $page_q->have_posts() ) : while ( $page_q->have_posts() ) : $page_q->the_post();

			// Nav Status
			$ns = get_post_meta( get_the_id(), 'np_nav_status', true);

			// Nested Pages Visibility
			$np_status = get_post_meta( get_the_id(), 'nested_pages_status', true );

			// Nav Title
			$nav_title = get_post_meta( get_the_id(), 'np_nav_title', true );
			$nav_title = ( $nav_title !== "" ) ? $nav_title : get_the_title();

			if ( ($ns == 'show') || ($ns == '') ) {
				if ( $np_status !== 'hide' ){
				$menu = wp_update_nav_menu_item($this->id, 0, array(
					'menu-item-title' => $nav_title,
					'menu-item-url' => get_the_permalink(),
					'menu-item-status' => 'publish',
					'menu-item-type' => 'post_type',
					'menu-item-object' => 'page',
					'menu-item-object-id' => get_the_id(),
					'menu-item-parent-id' => $menu_parent
				));
				$this->sync( get_the_id(), $menu );
				}
			}

		endwhile; endif; wp_reset_postdata();
		
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