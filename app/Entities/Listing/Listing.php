<?php 
namespace NestedPages\Entities\Listing;

use NestedPages\Helpers;
use NestedPages\Entities\Confirmation\ConfirmationFactory;
use NestedPages\Entities\Post\PostDataFactory;
use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\Listing\ListingRepository;
use NestedPages\Entities\Listing\ListingQuery;
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
	* Query Results
	* @var array of post objects (WP Query)
	*/
	private $all_posts;

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
	* Listing Query
	*/
	private $listing_query;

	/**
	* Confirmation Factory
	*/
	private $confirmation;

	/**
	* User Repository
	*/
	private $user;

	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* Post Type Settings
	* @var object from post type repo
	*/
	private $post_type_settings;

	/**
	* Assigned Pages for post types
	* @var array from post type repo
	*/
	private $assigned_pt_pages;

	/**
	* Plugin Integrations
	*/
	private $integrations;

	/**
	* Disabled Standard Fields
	*/
	private $disabled_standard_fields;

	/**
	* Sticky Posts
	* @var array
	*/
	private $sticky_posts;

	/**
	* Enabled Custom Fields
	*/
	private $enabled_custom_fields;

	public function __construct($post_type)
	{
		$this->setPostType($post_type);
		$this->setStickyPosts();
		$this->integrations = new IntegrationFactory;
		$this->post_repo = new PostRepository;
		$this->user = new UserRepository;
		$this->confirmation = new ConfirmationFactory;
		$this->post_type_repo = new PostTypeRepository;
		$this->listing_repo = new ListingRepository;
		$this->listing_query = new ListingQuery;
		$this->post_data_factory = new PostDataFactory;
		$this->settings = new SettingsRepository;
		$this->setTaxonomies();
		$this->setPostTypeSettings();
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
	* Set the Sticky Posts
	* @since 2.0.1
	*/
	private function setStickyPosts()
	{
		$this->sticky_posts = get_option('sticky_posts');
		if ( !$this->sticky_posts ) $this->sticky_posts = array();
	}

	/**
	* Set the Post Type Settings
	* @since 1.6.9
	*/
	private function setPostTypeSettings()
	{
		$this->post_type_settings = $this->post_type_repo->getSinglePostType($this->post_type->name);
		$this->assigned_pt_pages = $this->post_type_repo->getAssignedPages();
	}

	/**
	* Set the Quick Edit Field Options
	*/
	private function setStandardFields()
	{
		// The standard fields checkbox is explicitly not set
		if ( isset($this->post_type_settings->standard_fields_enabled) && !$this->post_type_settings->standard_fields_enabled ){
			$this->disabled_standard_fields = array();
			return;
		}

		if ( isset($this->post_type_settings->standard_fields) && is_array($this->post_type_settings->standard_fields) ){
			$this->disabled_standard_fields = $this->post_type_settings->standard_fields;
			foreach ( $this->post_type_settings->standard_fields as $key => $fields ){
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
		if ( $this->listing_repo->isSearch() ) $sortable = false;
		if ( $this->post_type_settings->disable_sorting ) $sortable = false;

		// Get array of child pages
		$children = array();
		$all_children = $pages;
		foreach($all_children as $child){
			array_push($children, $child->ID);
		}
		// Compare child pages with user's toggled pages
		$compared = array_intersect($this->listing_repo->visiblePages($this->post_type->name), $children);

		$list_classes = 'sortable visible nplist';
		if ( !$this->user->canSortPages() || !$sortable || $this->listing_repo->isSearch() ) $list_classes .= ' no-sort';
		if ( $this->integrations->plugins->wpml->installed && $this->integrations->plugins->wpml->getCurrentLanguage() == 'all' ) $list_classes .= ' no-sort';
		if ( $this->integrations->plugins->yoast->installed ) $list_classes .= ' has-yoast';
		if ( $this->listing_repo->isSearch() ) $list_classes .= ' np-search-results';

		// Primary List
		if ( $count == 0 ) {
			include( Helpers::view('partials/list-header') ); // List Header
			include( Helpers::view('partials/bulk-edit') ); // Bulk Edit
			echo '<ol class="' . $list_classes . '" id="np-' . $this->post_type->name . '">';
			return;
		}

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
		$this->post = $this->post_data_factory->build($post, $this->h_taxonomies, $this->f_taxonomies);
	}

	/**
	* Get count of published child posts
	* @param object $post
	*/
	private function publishedChildrenCount($post)
	{
		$publish_count = 0;
		foreach ( $this->all_posts as $p ){
			if ( $p->post_parent == $post->id && $p->post_status !== 'trash' ) $publish_count++;
		}
		return $publish_count;
	}

	/**
	* Loop through all the pages and create the nested / sortable list
	* Called in listing.php view
	*/
	private function getPosts()
	{
		$this->all_posts = $this->listing_query->getPosts($this->post_type, $this->h_taxonomies, $this->f_taxonomies);
		$this->listPostLevel();
		return;
	}

	/**
	* List a single tree node of posts
	*/
	private function listPostLevel($parent = 0, $count = 0, $level = 1)
	{
		$wpml = $this->integrations->plugins->wpml->installed;
		$wpml_current_language = null;
		if ( $wpml ) $wpml_current_language = $this->integrations->plugins->wpml->getCurrentLanguage();

		if ( !$this->listing_repo->isSearch() ){
			$pages = get_page_children($parent, $this->all_posts);
			if ( !$pages ) return;
			$parent_status = get_post_status($parent);
			$level++;
			if ( $parent_status !== 'trash' ) $this->listOpening($pages, $count);
		} else {
			$parent_status = null;
			$pages = $this->all_posts;
			echo '<ol class="sortable no-sort nplist visible">';
		}
		
		foreach($pages as $page) :

			if ( $page->post_parent !== $parent && !$this->listing_repo->isSearch() ) continue;
			$count++;

			global $post;
			$post = $page;
			$this->setPost($post);

			if ( $this->post->status !== 'trash' ) :

				echo '<li id="menuItem_' . esc_attr($this->post->id) . '" class="page-row';

				// Post Type
				echo ' post-type-' . esc_attr($this->post->post_type);

				// Assigned to manage a post type?
				if ( $this->listing_repo->isAssignedPostType($this->post->id, $this->assigned_pt_pages) ) echo ' is-page-assignment';

				// Published?
				if ( $this->post->status == 'publish' ) echo ' published';
				if ( $this->post->status == 'draft' ) echo ' draft';
				
				// Hidden in Nested Pages?
				if ( $this->post->np_status == 'hide' ) echo ' np-hide';

				// Taxonomies
				echo ' ' . $this->post_repo->getTaxonomyCSS($this->post, $this->h_taxonomies, $this->f_taxonomies);
				
				echo '">';
				
				$count++;

				$row_view = ( $this->post->type !== 'np-redirect' ) ? 'partials/row' : 'partials/row-link';

				// CSS Classes for the <li> row element
				$row_classes = '';
				if ( !$this->post_type->hierarchical ) $row_classes .= ' non-hierarchical';
				if ( !$this->user->canSortPages() ) $row_classes .= ' no-sort';
				if ( $wpml_current_language == 'all' ) $row_classes .= ' no-sort';
				if ( $this->listing_repo->isSearch() ) $row_classes .= ' search';

				// Page Assignment for Post Type
				$assigned_pt = ( $this->listing_repo->isAssignedPostType($this->post->id, $this->assigned_pt_pages) ) 
					? $this->listing_repo->assignedPostType($this->post->id, $this->assigned_pt_pages)
					: false;

				include( Helpers::view($row_view) );

			endif; // trash status
			
			if ( !$this->listing_repo->isSearch() ) $this->listPostLevel($page->ID, $count, $level);

			if ( $this->post->status !== 'trash' ) echo '</li>';

			if ( $this->publishedChildrenCount($this->post) > 0 && !$this->listing_repo->isSearch() && $continue_nest ) echo '</ol>';

		endforeach; // Loop
			
		if ( $parent_status !== 'trash' ) echo '</ol><!-- list close -->';
	}
}