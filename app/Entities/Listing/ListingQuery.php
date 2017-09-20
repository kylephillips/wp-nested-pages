<?php
namespace NestedPages\Entities\Listing;

use NestedPages\Entities\Listing\ListingRepository;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Config\SettingsRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Performs the query for the page listing and formats into a multidemensional array
*/
class ListingQuery
{
	private $sort_options;

	private $listing_repo;

	private $post_type_repo;

	private $settings;

	private $integrations;

	private $post_type;

	private $h_taxonomies;

	private $f_taxonomies;

	public function __construct()
	{
		$this->listing_repo = new ListingRepository;
		$this->post_type_repo = new PostTypeRepository;
		$this->settings = new SettingsRepository;
		$this->integrations = new IntegrationFactory;
		$this->setSortOptions();
	}

	/**
	* Set the Sort Options
	*/
	private function setSortOptions()
	{
		$this->sort_options = new \StdClass();
		$this->sort_options->orderby = isset($_GET['orderby'])
			? sanitize_text_field($_GET['orderby'])
			: 'menu_order';
		$this->sort_options->order = isset($_GET['order'])
			? sanitize_text_field($_GET['order'])
			: 'ASC';
		$this->sort_options->author = isset($_GET['author'])
			? sanitize_text_field($_GET['author'])
			: null;
	}

	/**
	* Get the Posts
	*/
	public function getPosts($post_type, $h_taxonomies = array(), $f_taxonomies = array())
	{
		$wpml = $this->integrations->plugins->wpml->installed;
		$this->h_taxonomies = $h_taxonomies;
		$this->f_taxonomies = $f_taxonomies;
		$this->post_type = $post_type;

		$this->setTaxonomyFilters();

		if ( $this->post_type->name == 'page' ) {
			$post_type = array('page');
			if ( !$this->settings->menusDisabled() && !$wpml ) $post_type[] = 'np-redirect';
		} else {
			$post_type = array($post_type->name);
		}
		
		$query_args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'author' => $this->sort_options->author,
			'orderby' => $this->sort_options->orderby,
			'post_status' => array('publish', 'pending', 'draft', 'private', 'future', 'trash'),
			'order' => $this->sort_options->order
		);
		
		if ( $this->listing_repo->isSearch() ) $query_args = $this->searchParams($query_args);
		if ( $this->listing_repo->isFiltered() ) $query_args = $this->filterParams($query_args);
		
		$query_args = apply_filters('nestedpages_page_listing', $query_args);
		
		add_filter( 'posts_clauses', array($this, 'queryFilter') );
		$all_posts = new \WP_Query($query_args);
		remove_filter( 'posts_clauses', array($this, 'queryFilter') );
		
		if ( $all_posts->have_posts() ) :
			return $all_posts->posts;
		endif; wp_reset_postdata();
		return null;
	}

	/**
	* Search Paramaters
	*/
	private function searchParams($query_args)
	{
		$query_args['post_title_like'] = sanitize_text_field($_GET['search']);
		unset($query_args['post_parent']);
		return $query_args;
	}

	/**
	* Filter Posts
	*/
	private function filterParams($query_args)
	{
		if ( !isset($_GET['category']) ) return $query_args;
		$query_args['cat'] = sanitize_text_field($_GET['category']);
		return $query_args;
	}

	/**
	* Add Taxonomy Filters to the sort options if applicable
	*/
	private function setTaxonomyFilters()
	{
		$taxonomies = array_merge($this->h_taxonomies, $this->f_taxonomies);
		$tax_query = array();
		foreach ( $taxonomies as $tax ) :
			if ( $this->post_type_repo->sortOptionEnabled($this->post_type->name, $tax->name, true) && isset($_GET[$tax->name]) ) :
				$tax_query[] = array(
					'taxonomy' => $tax->name,
					'fields' => 'term_id',
					'terms' => sanitize_text_field($_GET[$tax->name])
				);
			endif;
		endforeach;
		if ( !empty($tax_query) ) $this->sort_options->tax_query = $tax_query;
	}

	/**
	* Query filter to add taxonomies to return data
	* Fixes N+1 problem with taxonomies, eliminating need to query on every post
	*/
	public function queryFilter($pieces)
	{
		global $wpdb;
		
		// Add Hierarchical Categories
		foreach($this->h_taxonomies as $tax){
			$name = $tax->name;
			$name_simple = sanitize_text_field(str_replace('-', '', $tax->name));
			$tr = 'tr_' . $name_simple;
			$tt = 'tt_' . $name_simple;
			$t = 't_' . $name_simple;

			$pieces['join'] .= "
				LEFT JOIN `$wpdb->term_relationships` AS $tr ON $tr.object_id = $wpdb->posts.ID
				LEFT JOIN `$wpdb->term_taxonomy` AS $tt ON $tt.term_taxonomy_id = $tr.term_taxonomy_id AND $tt.taxonomy = '$name'
				LEFT JOIN `$wpdb->terms` AS $t ON $t.term_id = $tt.term_id";
			$pieces['fields'] .= ", GROUP_CONCAT(DISTINCT $t.term_id SEPARATOR ',') AS '$name'";
		}

		// Add Flat Categories
		foreach($this->f_taxonomies as $tax){
			$name = $tax->name;
			$name_simple = sanitize_text_field(str_replace('-', '', $tax->name));
			$tr = 'tr_' . $name_simple;
			$tt = 'tt_' . $name_simple;
			$t = 't_' . $name_simple;

			$pieces['join'] .= "
				LEFT JOIN `$wpdb->term_relationships` AS $tr ON $tr.object_id = $wpdb->posts.ID
				LEFT JOIN `$wpdb->term_taxonomy` AS $tt ON $tt.term_taxonomy_id = $tr.term_taxonomy_id AND $tt.taxonomy = '$name'
				LEFT JOIN `$wpdb->terms` AS $t ON $t.term_id = $tt.term_id";
			$pieces['fields'] .= ",GROUP_CONCAT(DISTINCT $t.term_id SEPARATOR ',') AS '$name'";
		}

		$pieces['groupby'] = "$wpdb->posts.ID"; 
		return $pieces;
	}
}