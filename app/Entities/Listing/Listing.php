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
use NestedPages\Entities\PostType\PostTypeCustomFields;

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
	* Custom Field Repository
	*/
	private $custom_fields_repo;

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
	* User status preference
	* @var array
	*/
	private $status_preference;

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
		$this->custom_fields_repo = new PostTypeCustomFields;
		$this->setTaxonomies();
		$this->setPostTypeSettings();
		$this->setStandardFields();
		$this->setStatusPreference();
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
		return [&$classinstance, "listPosts"];
	}

	/**
	* Get the Current Page URL
	*/
	private function pageURL()
	{
		$base = ( $this->post_type->name == 'post' ) ? admin_url('edit.php') : admin_url('admin.php');
		return $base . '?page=' . sanitize_text_field($_GET['page']);
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
		if ( !$this->sticky_posts ) $this->sticky_posts = [];
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
			$this->disabled_standard_fields = [];
			return;
		}

		if ( isset($this->post_type_settings->standard_fields) && is_array($this->post_type_settings->standard_fields) ){
			$this->disabled_standard_fields = $this->post_type_settings->standard_fields;
			foreach ( $this->post_type_settings->standard_fields as $key => $fields ){
				if ( $key == 'standard' ) $this->disabled_standard_fields = $fields;
			}
			return;
		}
		$this->disabled_standard_fields = [];
		return;
	}

	/**
	* Get the Post States
	*/
	private function postStates($assigned_pt)
	{
		$out = '';
		$post_states = [];
		if ( !$assigned_pt ) {
			if ( $this->post->id == get_option('page_on_front') ) $post_states['page_on_front'] = '&ndash; ' . __('Front Page', 'wp-nested-pages');
			if ( $this->post->id == get_option('page_for_posts') ) $post_states['page_for_posts'] = '&ndash; ' . __('Posts Page', 'wp-nested-pages');
		}
		$post_states = apply_filters('display_post_states', $post_states, $this->post);
		if ( empty($post_states) ) return $out;
		$state_count = count($post_states);
		$i = 0;
		foreach ( $post_states as $state ) {
			++$i;
			( $i == $state_count ) ? $sep = '' : $sep = ',';
			$out .= " <em class='np-page-type'><strong>$state</strong>$sep</em>";
		}
		return $out;
	}

	/**
	* Row Actions
	* Adds assigned pt actions as well as any custom actions registered through page_row_actions filter
	*/
	private function rowActions($assigned_pt)
	{
		$actions = [];
		if ( $assigned_pt ) {
			if ( current_user_can('publish_posts') ) $actions['add_new'] = '<a href="' . $this->post_type_repo->addNewPostLink($assigned_pt->name) . '">' . $assigned_pt->labels->add_new . '</a>';
			$actions['view_all'] = '<a href="' .  $this->post_type_repo->allPostsLink($assigned_pt->name) . '">' . $assigned_pt->labels->all_items . ' (' . $this->listing_repo->postCount($assigned_pt->name) . ')</a>';
		}
		$actions = apply_filters('post_row_actions', $actions, $this->post);
		if ( $this->post_type->name == 'page' ) $actions = apply_filters('page_row_actions', $actions, $this->post);
		if ( empty($actions) ) return null;
		$out = '<ul class="np-assigned-pt-actions">';
		foreach ( $actions as $key => $action ){
			$out .= '<li class="' . $key;
			if ( $key == 'add_new' || $key == 'view_all' ) $out .= ' visible';
			$out .= '">' . $action . '</li>';
		}		
		$out .= '</ul>';
		return $out;
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
	* Set the user status preference
	*/
	private function setStatusPreference()
	{
		$this->status_preference = $this->user->getStatusPreference($this->post_type->name);
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
		$children = [];
		$all_children = $pages;
		foreach($all_children as $child){
			array_push($children, $child->ID);
		}
		// Compare child pages with user's toggled pages
		$compared = array_intersect($this->listing_repo->visiblePages($this->post_type->name), $children);

		$list_classes = 'sortable visible nplist';
		if ( !$this->user->canSortPosts($this->post_type->name) || !$sortable || $this->listing_repo->isSearch() ) $list_classes .= ' no-sort';
		if ( $this->listing_repo->isOrdered($this->post_type->name) ) $list_classes .= ' no-sort';
		if ( $this->integrations->plugins->wpml->installed && $this->integrations->plugins->wpml->getCurrentLanguage() == 'all' ) $list_classes .= ' no-sort';
		if ( $this->integrations->plugins->yoast->installed ) $list_classes .= ' has-yoast';
		if ( $this->listing_repo->isSearch() ) $list_classes .= ' np-search-results';
		if ( $this->settings->nonIndentEnabled() ) $list_classes .= ' non-indent';

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

		if ( $this->listing_repo->isFiltered() ){
			$parent_status = null;
			$pages = $this->all_posts;
			$level++;
			echo '<ol class="sortable nplist visible filtered">';
		} elseif ( !$this->listing_repo->isSearch() ) {
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
		if ( !$pages ) return;
		
		foreach($pages as $page) :

			if ( $page->post_parent !== $parent && !$this->listing_repo->isSearch() && !$this->listing_repo->isFiltered() ) continue;
			$count++;

			global $post;
			$post = $page;
			$this->setPost($post);

			if ( $this->post->status !== 'trash' ) :

				$row_parent_classes = 'page-row';
				// Post Type
				$row_parent_classes .= ' post-type-' . esc_attr($this->post->post_type);

				// Managed Post Type page?
				if ( $this->listing_repo->isAssignedPostType($this->post->id, $this->assigned_pt_pages) ) $row_parent_classes .= ' is-page-assignment';

				// Published?
				if ( $this->post->status == 'publish' ) $row_parent_classes .= ' published';
				if ( $this->post->status == 'draft' ) $row_parent_classes .=  ' draft';

				// Hidden in Nested Pages?
				if ( $this->post->np_status == 'hide' ) $row_parent_classes .= ' np-hide';

				// User Status Preference
				if ( $this->status_preference == 'published' && $this->post->status == 'draft' ) $row_parent_classes .= ' np-hide';
				if ( $this->status_preference == 'draft' && $this->post->status !== 'draft' ) $row_parent_classes .= ' np-hide';

				// Taxonomies
				$row_parent_classes .= ' ' . $this->post_repo->getTaxonomyCSS($this->post, $this->h_taxonomies, $this->f_taxonomies);

				echo '<li id="menuItem_' . esc_attr($this->post->id) . '" class="' . apply_filters('nestedpages_row_parent_css_classes', $row_parent_classes, $this->post, $this->post_type) . '">';
				
				$count++;

				$row_view = ( $this->post->type !== 'np-redirect' ) ? 'partials/row' : 'partials/row-link';

				// CSS Classes for the <li> row element
				$template = ( $this->post->template )
					? ' tpl-' .  sanitize_html_class( str_replace('.php', '', $this->post->template ) )
					: '';

				$row_classes = '';
				if ( !$this->post_type->hierarchical ) $row_classes .= ' non-hierarchical';
				if ( !$this->user->canSortPosts($this->post_type->name) ) $row_classes .= ' no-sort';
				if ( $wpml_current_language == 'all' ) $row_classes .= ' no-sort';
				if ( $this->listing_repo->isSearch() || $this->listing_repo->isOrdered($this->post_type->name) ) $row_classes .= ' search';
				if ( $this->post->template ) $row_classes .= $template;

				// Filter sortable per post
				$filtered_sortable = apply_filters('nestedpages_post_sortable', true, $this->post, $this->post_type);
				if ( !$filtered_sortable && $this->user->canSortPosts($this->post_type->name) && $this->post_type->hierarchical && !$wpml_current_language ) $row_classes .= ' no-sort-filtered';

				// Page Assignment for Post Type
				$assigned_pt = ( $this->listing_repo->isAssignedPostType($this->post->id, $this->assigned_pt_pages) ) 
					? $this->listing_repo->assignedPostType($this->post->id, $this->assigned_pt_pages)
					: false;

				include( Helpers::view($row_view) );

			endif; // trash status
			
			if ( !$this->listing_repo->isSearch() && !$this->listing_repo->isFiltered() ) $this->listPostLevel($page->ID, $count, $level);

			if ( $this->post->status !== 'trash' ) echo '</li>';

			if ( $this->publishedChildrenCount($this->post) > 0 && !$this->listing_repo->isSearch() && !$this->listing_repo->isFiltered() ) echo '</ol>';

		endforeach; // Loop
			
		if ( $parent_status !== 'trash' ) echo '</ol><!-- list close -->';
	}
}
