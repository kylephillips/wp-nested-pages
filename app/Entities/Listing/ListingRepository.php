<?php
namespace NestedPages\Entities\Listing;

use NestedPages\Entities\PluginIntegration\IntegrationFactory;

class ListingRepository 
{
	/**
	* Plugin Integrations
	*/
	private $integrations;

	public function __construct()
	{
		$this->integrations = new IntegrationFactory;
	}

	/**
	* User's Toggled Pages
	*/
	public function visiblePages($post_type)
	{
		$visible = unserialize(get_user_meta(get_current_user_id(), 'np_visible_posts', true));
		if ( !isset($visible[$post_type]) ) $visible[$post_type] = array();
		return $visible[$post_type];
	}

	/**
	* Taxonomies
	*/
	public function taxonomies()
	{
		$taxonomies = get_taxonomies(array(
			'public' => true,
		), 'objects');
		return $taxonomies;
	}

	/**
	* Get all non-empty Terms for a given taxonomy
	*/
	public function terms($taxonomy)
	{
		return get_terms($taxonomy);
	}

	/**
	* Post Types
	*/
	public function postTypes()
	{
		$types = get_post_types(array(
			'public' => true
		), 'objects');
		return $types;
	}

	/**
	* Recent Posts for a given post type
	*/
	public function recentPosts($post_type)
	{
		$pq = new \WP_Query(array(
			'post_type' => $post_type,
			'posts_per_page' => 10
		));
		if ( $pq->have_posts() ) :
			return $pq->posts;
		else : 
			return false;
		endif; wp_reset_postdata();
	}

	/**
	* Is the page, or it's parent translation, assigned a post type?
	* @param $page - int - post id
	* @param $assigned_pages - array
	* @return bool
	*/
	public function isAssignedPostType($page_id, $assigned_pages)
	{
		if ( $this->integrations->plugins->wpml->installed && !$this->integrations->plugins->wpml->isDefaultLanguage() ){
			$page_id = $this->integrations->plugins->wpml->getPrimaryLanguagePost($page_id);
		}
		if ( array_key_exists($page_id, $assigned_pages) ) return true;
		return false;
	}

	/**
	* Get the assigned post type for a page
	* @param $page - int - post id
	* @param $assigned_pages - array
	* @return post type object
	*/
	public function assignedPostType($page_id, $assigned_pages)
	{
		if ( $this->integrations->plugins->wpml->installed && !$this->integrations->plugins->wpml->isDefaultLanguage() ){
			$page_id = $this->integrations->plugins->wpml->getPrimaryLanguagePost($page_id);
		}
		return get_post_type_object($assigned_pages[$page_id]);
	}

	/**
	* Get the number of published posts for a given post type
	*/
	public function postCount($post_type)
	{
		if ( $this->integrations->plugins->wpml->installed ){
			return $this->integrations->plugins->wpml->getPostTypeCountByLanguage($post_type);
		}
		return wp_count_posts($post_type)->publish;
	}

	/**
	* Is this a search
	* @return boolean
	*/
	public function isSearch()
	{
		return ( isset($_GET['search']) && $_GET['search'] !== "" ) ? true : false;
	}

	/**
	* Is the list filtered?
	*/ 
	public function isFiltered()
	{
		return ( isset($_GET['category']) && $_GET['category'] !== "all" ) ? true : false;
	}
}