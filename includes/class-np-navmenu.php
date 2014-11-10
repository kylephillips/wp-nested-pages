<?php
require_once('class-np-helpers.php');
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

	/**
	* Menu Name Option
	*/
	private $menu_name;


	public function __construct()
	{
		$this->getMenuName();
		$this->setID();
		$this->setItems();
	}


	/**
	* Get Optional custom menu name
	* @since 1.1.11
	*/
	private function getMenuName()
	{
		$this->menu_name = ( get_option('nestedpages_menu') )
			? get_option('nestedpages_menu')
			: 'nestedpages';
	}


	/**
	* Set the Menu ID
	*/
	public function setID()
	{
		$menu = get_term_by('name', $this->menu_name, 'nav_menu');
		if ( $menu ) {
			$this->id = $menu->term_id;
		} else {
			$this->addMenu();
		}
	}


	/**
	* Add the Nav Menu
	*/
	public function addMenu()
	{
		$menu = wp_create_nav_menu($this->menu_name);
		$this->id = $menu;
	}


	/**
	* Set the Menu Items
	*/
	public function setItems()
	{
		$menu = get_term_by('name', $this->menu_name, 'nav_menu');
		if ( $menu ) $this->items = wp_get_nav_menu_items($this->id);
	}


	/**
	* Verify URL Format
	* @param string - URL to check
	* @return string - formatted URL
	*/
	private function check_url($url)
	{
		$parsed = parse_url($url);
		if (empty($parsed['scheme'])) $url = 'http://' . ltrim($url, '/');
		return $url;
	}


	/**
	* Create the menu with nested pages (Recursive function)
	*/
	public function sync($parent = 0, $menu_parent = 0)
	{
		$page_q = new WP_Query(array(
			'post_type' => array('page','np-redirect'),
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

			// Link Target
			$link_target = get_post_meta( get_the_id(), 'np_link_target', true );

			// Title Attribue
			$title_attribute = get_post_meta( get_the_id(), 'np_title_attribute', true );

			// CSS Classes
			$css_classes = get_post_meta( get_the_id(), 'np_nav_css_classes', true );

			// Nav Title
			$nav_title = get_post_meta( get_the_id(), 'np_nav_title', true );
			$nav_title = ( $nav_title !== "" ) ? $nav_title : get_the_title();

			if ( ($ns == 'show') || ($ns == '') ) {
				if ( $np_status !== 'hide' ){

					if ( get_post_type() == 'page' ){
						$menu = wp_update_nav_menu_item($this->id, 0, array(
							'menu-item-title' => $nav_title,
							'menu-item-url' => get_the_permalink(),
							'menu-item-attr-title' => $title_attribute,
							'menu-item-status' => 'publish',
							'menu-item-classes' => $css_classes,
							'menu-item-type' => 'post_type',
							'menu-item-object' => 'page',
							'menu-item-object-id' => get_the_id(),
							'menu-item-parent-id' => $menu_parent,
							'menu-item-target' => $link_target
						));
					} else { // redirect
						$menu = wp_update_nav_menu_item($this->id, 0, array(
							'menu-item-title' => $nav_title,
							'menu-item-url' => NP_Helpers::check_url(get_the_content()),
							'menu-item-attr-title' => $title_attribute,
							'menu-item-status' => 'publish',
							'menu-item-classes' => $css_classes,
							'menu-item-type' => 'custom',
							'menu-item-object' => 'page',
							'menu-item-object-id' => get_the_id(),
							'menu-item-parent-id' => $menu_parent,
							'menu-item-target' => $link_target
						));
					}

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