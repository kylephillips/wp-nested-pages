<?php 

namespace NestedPages\Entities\NavMenu;

class NavMenuRepository 
{

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
	* @param string $query - xfn/object_id
	* @return int
	*/
	public function getMenuItem($id, $query = 'xfn')
	{	
		global $wpdb;
		$post_id = 0;
		
		if ( $query == 'xfn' ){
			$prefix = $wpdb->prefix;
			$meta_table = $prefix . 'postmeta';
			$sql = "SELECT post_id FROM `$meta_table` WHERE meta_value = '$id' AND meta_key = '_menu_item_xfn'";
			$post_id = $wpdb->get_var($sql);
			return ( $post_id ) ? $post_id : 0;
		}

		if ( $query == 'object_id' ){
			$menu_id = $this->getMenuID();
			$prefix = $wpdb->prefix;
			$meta_table = $prefix . 'postmeta';
			$term_relationships_table = $prefix . 'term_relationships';
			$term_taxonomy_table = $prefix . 'term_taxonomy';
			$terms_table = $prefix . 'terms';
			$sql = "SELECT
				pm.post_id,
				t.term_id,
				t.name,
				pmx.meta_value AS xfn_type
				FROM $meta_table AS pm
				LEFT JOIN $term_relationships_table AS tr
				ON tr.object_id = pm.post_id
				LEFT JOIN $term_taxonomy_table AS tt
				ON tt.term_taxonomy_id = tr.term_taxonomy_id
				LEFT JOIN $terms_table AS t
				ON t.term_id = tt.term_id
				LEFT JOIN $meta_table AS pmx
				ON pmx.post_id = pm.post_id AND pmx.meta_key = '_menu_item_xfn'
				WHERE pm.meta_value = $id AND pm.meta_key = '_menu_item_object_id'
			";
			$results = $wpdb->get_results($sql);
			foreach($results as $result){
				if ( $result->term_id == $menu_id && $result->xfn_type == 'page' ) $post_id = $result->post_id;
			}
			return $post_id;
		}
	}

	private function getMenuItemFromXFN($id)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
			$meta_table = $prefix . 'postmeta';
			$sql = "SELECT post_id FROM `$meta_table` WHERE meta_value = $id AND meta_key = '_menu_item_xfn'";
			$post_id = $wpdb->get_var($sql);
			
			$wpdb = $original_wpdb;
			return ( $post_id ) ? $post_id : 0;
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