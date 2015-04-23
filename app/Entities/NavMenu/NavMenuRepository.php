<?php namespace NestedPages\Entities\NavMenu;

class NavMenuRepository {

	/**
	* Get the Menu ID
	* @since 1.3.4
	* @return int
	*/
	public function getMenuID()
	{
		$term = $this->getMenuTermObject();
		return $term->term_id;
	}


	/**
	* Get Menu Item ID
	* @since 1.3.4
	* @param int $id - Post ID
	* @return int
	*/
	public function getMenuItemID($id)
	{	
		$meta_query = new \WP_Query(array(
			'post_type' => 'nav_menu_item',
			'posts_per_page' => 1,
			'meta_key' => '_menu_item_object_id',
			'meta_value' => $id,
			'tax_query' => array(
				array(
					'taxonomy' => 'nav_menu',
					'field'    => 'id',
					'terms'    => $this->getMenuID(),
				),
			),
		));
		return ( $meta_query->have_posts() ) ? 
			$meta_query->posts[0]->ID : 
			$this->getLinkMenuItemXFN($id);
	}


	/**
	* Get Link from XFN field
	* Using XFN field to store original post ID
	* Hack way of doing it, but no other way to tie custom menu items to post type and retain custom functionality
	* @param int $id - Post ID
	*/
	public function getLinkMenuItemXFN($id)
	{
		$meta_query = new \WP_Query(array(
			'post_type' => 'nav_menu_item',
			'posts_per_page' => 1,
			'meta_key' => '_menu_item_xfn',
			'meta_value' => $id,
		));
		return ( $meta_query->have_posts() ) ? $meta_query->posts[0]->ID : $this->getLinkMenuItemID($id);
	}


	/**
	* Get Link Nav Menu from post ID using title
	* Supporting legacy NP versions before XFN was saved
	* @since 1.3.4
	* @param int $id
	* @return int
	*/
	public function getLinkMenuItemID($id)
	{
		$post = get_page_by_title( get_the_title($id), OBJECT, 'nav_menu_item');
		if ( !$post ) return 0;
		return $post->ID;
	}



	/**
	* Get the Menu Term Object
	* @since 1.3.4
	* @return object - WP Term Object
	*/
	public function getMenuTermObject()
	{
		$menu_id = get_option('nestedpages_menu');
		$term = get_term_by('id', $menu_id, 'nav_menu');
		if ( $term ) return get_term_by('id', $menu_id, 'nav_menu');
		
		// No Menu Yet		
		$this->createNewMenu();
		return $this->getMenuTermObject();
	}


	/**
	* Get the Menu ID from the title
	* @since 1.3.5
	* @return int
	*/
	public function getMenuIDFromTitle($title)
	{
		$term = get_term_by('name', $title, 'nav_menu');
		return ( $term ) ? $term->term_id : false;
	}


	/**
	* Create Empty Menu if one doesn't exist
	* @since 1.3.4
	*/
	private function createNewMenu()
	{
		$menu_id = wp_create_nav_menu('Nested Pages');
		update_option('nestedpages_menu', $menu_id);
	}


	/**
	* Clear out the menu
	*/
	public function clearMenu($menu_id)
	{
		$menu_items = wp_get_nav_menu_items($menu_id);
		foreach ( $menu_items as $i ){
			wp_delete_post($i->ID, true);
		}
	}


	/**
	* Is the provided post a nav menu item
	* @return boolean
	* @param int $id - post id
	*/
	public function isNavMenuItem($id)
	{
		if ( get_post_type($id) == 'nav_menu_item' ) return true;
		return false;
	}


	/**
	* Get the Link post id from a title
	*/
	public function getLinkfromTitle($title)
	{
		$post = get_page_by_title($title, OBJECT, 'np-redirect');
		return $post->ID;
	}


	/**
	* Get an array of pages not hidden in nav menu
	* WP_Query won't return pages with empty meta values, so sql is used
	* @return array
	*/
	public function getPagesInMenu()
	{
		global $wpdb;
		$post_table = $wpdb->prefix . 'posts';
		$meta_table = $wpdb->prefix . 'postmeta';
		$sql = "SELECT p.ID AS nav_status FROM $post_table AS p LEFT JOIN $meta_table AS m ON p.ID = m.post_id AND m.meta_key = 'np_nav_status' WHERE p.post_type = 'page' AND (m.meta_value = 'show' OR m.meta_value IS NULL)";
		$results = $wpdb->get_results($sql, ARRAY_N);
		if ( !$results ) return;
		foreach($results as $key => $result){
			$visible[$key] = $result[0];
		}
		return $visible;
	}

}