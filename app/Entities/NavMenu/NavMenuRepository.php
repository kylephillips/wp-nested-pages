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
		));
		return ( $meta_query->have_posts() ) ? $meta_query->posts[0]->ID : $this->getLinkMenuItemXFN($id);
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
		if ( get_option('nestedpages_menu') ){
			$menu_id = get_option('nestedpages_menu');
			return get_term_by('id', $menu_id, 'nav_menu');
		} else {
			$this->createNewMenu();
			$this->getMenuTermObject();
		}
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

}