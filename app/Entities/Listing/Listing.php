<?php 

namespace NestedPages\Entities\Listing;

use NestedPages\Helpers;
use NestedPages\Entities\Confirmation\ConfirmationFactory;
use NestedPages\Entities\Post\PostDataFactory;
use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\Listing\ListingRepository;
use NestedPages\Config\SettingsRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Primary Post Listing
*/
class Listing 
{

	/**
	* Post Type
	* @var object WP Post Type Object
	*/
	private $post_type;

	/**
	* Hierarchical Taxonomies
	* @var array
	*/
	private $h_taxonomies;

	/**
	* Flat Taxonomies
	* @var array
	*/
	private $f_taxonomies;

	/**
	* Post Data Factory
	*/
	private $post_data_factory;

	/**
	* Post Data
	* @var object
	*/
	private $post;

	/**
	* Post Repository
	*/
	private $post_repo;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Listing Repository
	*/
	private $listing_repo;

	/**
	* Confirmation Factory
	*/
	private $confirmation;

	/**
	* User Repository
	*/
	private $user;

	/**
	* Sorting Options
	* @var array
	*/
	private $sort_options;

	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* Plugin Integrations
	*/
	private $integrations;

	/**
	* Disabled Standard Fields
	*/
	private $disabled_standard_fields;

	/**
	* Enabled Custom Fields
	*/
	private $enabled_custom_fields;


	public function __construct($post_type)
	{
		$this->setPostType($post_type);
		$this->integrations = new IntegrationFactory;
		$this->post_repo = new PostRepository;
		$this->user = new UserRepository;
		$this->confirmation = new ConfirmationFactory;
		$this->post_type_repo = new PostTypeRepository;
		$this->listing_repo = new ListingRepository;
		$this->post_data_factory = new PostDataFactory;
		$this->settings = new SettingsRepository;
		$this->setStandardFields();
	}

	/**
	* Called by Menu Class
	* Instantiates Listing Class
	* @since 1.2.0
	*/
	public static function admin_menu($post_type)
	{
		$class_name = get_class();
		$classinstance = new $class_name($post_type);
		return array(&$classinstance, "listPosts");
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
	* Get the Current Page URL
	*/
	private function pageURL()
	{
		$base = ( $this->post_type->name == 'post' ) ? admin_url('edit.php') : admin_url('admin.php');
		return $base . '?page=' . $_GET['page'];
	}

	/**
	* Set the Post Type
	* @since 1.1.16
	*/
	private function setPostType($post_type)
	{
		$this->post_type = get_post_type_object($post_type);
	}

	/**
	* Set the Quick Edit Field Options
	*/
	private function setStandardFields()
	{
		$type_options = $this->post_type_repo->getSinglePostType($this->post_type->name);

		// The standard fields checkbox is explicitly not set
		if ( isset($type_options->standard_fields_enabled) && !$type_options->standard_fields_enabled ){
			$this->disabled_standard_fields = array();
			return;
		}

		if ( isset($type_options->standard_fields) && is_array($type_options->standard_fields) ){
			$this->disabled_standard_fields = $type_options->standard_fields;
			foreach ( $type_options->standard_fields as $key => $fields ){
				if ( $key == 'standard' ) $this->disabled_standard_fields = $fields;
			}
			return;
		}
		$this->disabled_standard_fields = array();
		return;
	}

	/**
	* The Main View
	* Replaces Default Post Listing
	*/
	public function listPosts()
	{
		$this->setSortOptions();
		include( Helpers::view('listing') );
	}

	/**
	* Set the Taxonomies for Post Type
	*/
	private function setTaxonomies()
	{
		$this->h_taxonomies = $this->post_type_repo->getTaxonomies($this->post_type->name, true);
		$this->f_taxonomies = $this->post_type_repo->getTaxonomies($this->post_type->name, false);
	}

	/**
	* Opening list tag <ol>
	* @param array $pages - array of page objects from current query
	* @param int $count - current count in loop
	*/
	private function listOpening($pages, $count, $sortable = true)
	{
		if ( $this->isSearch() ) $sortable = false;

		// Get array of child pages
		$children = array();
		$all_children = $pages->posts;
		foreach($all_children as $child){
			array_push($children, $child->ID);
		}
		// Compare child pages with user's toggled pages
		$compared = array_intersect($this->listing_repo->visiblePages($this->post_type->name), $children);

		$list_classes = 'sortable visible nplist';
		if ( !$this->user->canSortPages() && !$sortable || $this->isSearch() ) $list_classes .= ' no-sort';
		if ( $this->integrations->plugins->yoast->installed ) $list_classes .= ' has-yoast';
		if ( $this->isSearch() ) $list_classes .= ' np-search-results';

		// Primary List
		if ( $count == 1 ) {
			include( Helpers::view('partials/list-header') ); // List Header
			echo '<ol class="' . $list_classes . '" id="np-' . $this->post_type->name . '">';
			return;
		}

		// Don't create new list for child elements of posts in trash
		if ( get_post_status($pages->query['post_parent']) == 'trash' ) return;

		echo '<ol class="nplist';
		if ( count($compared) > 0 ) echo ' visible" style="display:block;';
		echo '" id="np-' . $this->post_type->name . '">';	
		 
	}

	/**
	* Set Post Data
	* @param object post object
	*/
	private function setPost($post)
	{
		$this->post = $this->post_data_factory->build($post);
	}

	/**
	* Get count of published posts
	* @param object $pages (WP Query object)
	*/
	private function publishCount($pages)
	{
		$publish_count = 1;
		if ( $this->parentTrashed($pages) ) return;
		foreach ( $pages->posts as $p ){
			if ( $p->post_status !== 'trash' ) $publish_count++;
		}
		return $publish_count;
	}

	/**
	* Is this a search
	* @return boolean
	*/
	private function isSearch()
	{
		return ( isset($_GET['search']) && $_GET['search'] !== "" ) ? true : false;
	}

	/**
	* Is the list filtered?
	*/ 
	private function isFiltered()
	{
		return ( isset($_GET['category']) && $_GET['category'] !== "all" ) ? true : false;
	}

	/**
	* Loop through all the pages and create the nested / sortable list
	* Recursive Method, called in page.php view
	*/
	private function loopPosts($parent_id = 0, $count = 0, $nest_count = 0)
	{
		$this->setTaxonomies();
		
		if ( $this->post_type->name == 'page' ) {
			$post_type = array('page');
			if ( !$this->settings->menusDisabled() ) $post_type[] = 'np-redirect';
		} else {
			$post_type = array($this->post_type->name);
		}
		
		$query_args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'author' => $this->sort_options->author,
			'orderby' => $this->sort_options->orderby,
			'post_status' => array('publish', 'pending', 'draft', 'private', 'future', 'trash'),
			'post_parent' => $parent_id,
			'order' => $this->sort_options->order
		);
		
		if ( $this->isSearch() ) $query_args = $this->searchParams($query_args);
		if ( $this->isFiltered() ) $query_args = $this->filterParams($query_args);

		$pages = new \WP_Query(apply_filters('nestedpages_page_listing', $query_args, $nest_count));
		
		if ( $pages->have_posts() ) :
			$count++;
			$nest_count++;

			if ( $this->publishCount($pages) > 1 ){
				$this->listOpening($pages, $count);			
			}
			
			while ( $pages->have_posts() ) : $pages->the_post();

				global $post;
				$this->setPost($post);

				if ( $this->post->status !== 'trash' ) :

					echo '<li id="menuItem_' . $this->post->id . '" class="page-row';

					// Post Type
					echo ' post-type-' . $this->post->post_type;

					// Published?
					if ( $this->post->status == 'publish' ) echo ' published';
					if ( $this->post->status == 'draft' ) echo ' draft';
					
					// Hidden in Nested Pages?
					if ( $this->post->np_status == 'hide' ) echo ' np-hide';

					// Taxonomies
					echo ' ' . $this->post_repo->getTaxonomyCSS($this->post->id, $this->h_taxonomies);
					echo ' ' . $this->post_repo->getTaxonomyCSS($this->post->id, $this->f_taxonomies, false);
					
					echo '">';
					
					$count++;

					$row_view = ( $this->post->type !== 'np-redirect' ) ? 'partials/row' : 'partials/row-link';
					include( Helpers::view($row_view) );

				endif; // trash status
				
				if ( !$this->isSearch() ) $this->loopPosts($this->post->id, $count, $nest_count);

				if ( $this->post->status !== 'trash' ) {
					echo '</li>';
				}				

			endwhile; // Loop
			
			if ( $this->publishCount($pages) > 1 ){
				echo '</ol>';
			}

		endif; wp_reset_postdata();
	}

	/**
	* Search Posts
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
	* Parent Trash Status
	* @param WP Query object
	* @return boolean
	*/
	private function parentTrashed($pages)
	{
		if ( !isset($pages->query['post_parent']) || $pages->query['post_parent'] == 0 ) return false;
		if ( get_post_status($pages->query['post_parent']) == 'trash' ) return true;
		return false;

	}

}