<?php
namespace NestedPages\Entities\Listing;

use NestedPages\Entities\PluginIntegration\IntegrationFactory;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Config\SettingsRepository;

class ListingRepository 
{
	/**
	* Plugin Integrations
	*/
	public $integrations;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Settings Repository
	*/
	public $settings;

	public function __construct()
	{
		$this->integrations = new IntegrationFactory;
		$this->post_type_repo = new PostTypeRepository;
		$this->settings = new SettingsRepository;
	}

	/**
	* User's Toggled Pages
	*/
	public function visiblePages($post_type)
	{
		$meta = get_user_meta(get_current_user_id(), 'np_visible_posts', true);
		if ( $meta == '1' ) return [];
		$visible = unserialize($meta);
		if ( !$visible || !isset($visible[$post_type]) ) $visible[$post_type] = [];
		return $visible[$post_type];
	}

	/**
	* Taxonomies
	*/
	public function taxonomies()
	{
		$args = apply_filters('nestedpages_listing_taxonomies', ['public' => true]);
		$taxonomies = get_taxonomies($args, 'objects');
		return $taxonomies;
	}

	/**
	* Get all non-empty Terms for a given taxonomy
	*/
	public function terms($taxonomy)
	{
		$args = apply_filters('nestedpages_listing_taxonomy_terms', []);
		return get_terms($taxonomy, $args);
	}

	/**
	* Post Types
	*/
	public function postTypes()
	{
		$types = get_post_types([
			'public' => true
		], 'objects');
		return $types;
	}

	/**
	* Recent Posts for a given post type
	*/
	public function recentPosts($post_type)
	{
		$pq = new \WP_Query([
			'post_type' => $post_type,
			'posts_per_page' => 10
		]);
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
		$taxonomies = get_taxonomies();
		$tax_filtered = false;
		foreach ( $taxonomies as $tax ){
			if ( isset($_GET[$tax]) && $_GET[$tax] !== '' ) $tax_filtered = true;
		}
		return ( (isset($_GET['category']) && $_GET['category'] !== "all") || $tax_filtered ) ? true : false;
	}

	/**
	* Is the list ordered?
	*/ 
	public function isOrdered($post_type = null)
	{
		$ordered = ( isset($_GET['orderby']) && $_GET['orderby'] !== "" ) ? true : false;
		if ( $post_type ){
			$initial_orderby = $this->post_type_repo->defaultSortOption($post_type, 'orderby');
			if ( $initial_orderby ) $ordered = true;
		}
		if ( $ordered && isset($_GET['orderby']) && $_GET['orderby'] == 'menu_order' && !isset($_GET['order']) ) $ordered = false;
		// Enables nesting if sorted by menu order in ascending order
		if ( isset($_GET['orderby']) && $_GET['orderby'] == 'menu_order' && isset($_GET['order']) && $_GET['order'] == 'ASC' ) $ordered = false;
		return $ordered;
	}

	/**
	* Do we show the "link" interface/functionality?
	* @param $post_type - obj (WP_Post_Type)
	* @param $user - obj (NestedPages\Entities\User\UserRepository)
	* @return bool
	*/
	public function showLinks($post_type, $user)
	{
		$show_links = ( $user->canPublish($post_type->name) 
			&& $post_type->name == 'page' 
			&& !$this->isSearch() 
			&& !$this->isOrdered($post_type->name) 
			&& !$this->settings->menusDisabled() 
			&& !$this->integrations->plugins->wpml->installed ) 
		? true : false;
		return apply_filters('nestedpages_show_links', $show_links, $post_type, $user, $this);
	}
}