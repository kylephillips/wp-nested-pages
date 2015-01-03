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