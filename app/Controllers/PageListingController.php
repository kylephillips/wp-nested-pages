<?php namespace NestedPages\Controllers;

use NestedPages\Helpers;
use NestedPages\Entities\Confirmation\ConfirmationFactory;
use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\User\UserRepository;

/**
* Primary Listing
* Initiates Page Listing screen (overwriting default), and displays primary plugin view.
*/
class PageListingController {

	/**
	* Post Type
	* @var object
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
	* Post Data
	* @var array
	*/
	private $post_data;


	/**
	* Post Repository
	*/
	private $post_repo;


	/**
	* Confirmation Factory
	*/
	private $confirmation;


	/**
	* User Repository
	*/
	private $user;


	public function __construct()
	{
		$this->post_repo = new PostRepository;
		$this->user = new UserRepository;
		$this->confirmation = new ConfirmationFactory;
		$this->setPostType();
		
	}


	/**
	* Called by Menu Class
	* @since 1.2
	*/
	public static function admin_menu() {
		$class_name = get_class();
		$classinstance = new $class_name();
		return array(&$classinstance, "pageListing");
	}


	/**
	* Set the Post Type & verify it exists
	* @since 1.1.16
	*/
	private function setPostType()
	{
		$this->post_type = get_post_type_object('page');
	}


	/**
	* Add New Page Link
	* @return string
	*/
	private function addNewPageLink()
	{
		return esc_url( admin_url('post-new.php?post_type=page') );
	}


	/**
	* User's Toggled Pages
	*/
	private function visiblePages()
	{
		$visible = unserialize(get_user_meta(get_current_user_id(), 'np_visible_pages', true));
		if ( !$visible ) $visible = array();
		return $visible;
	}


	/**
	* The Main View
	* Replaces Default Pages Listing
	*/
	public function pageListing()
	{
		include( Helpers::view('pages') );
	}


	/**
	* Set the Taxonomies for Pages
	*/
	private function setTaxonomies()
	{
		$taxonomy_names = get_object_taxonomies( $this->post_type->name );
		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( $taxonomy_names as $taxonomy_name ) {
			$taxonomy = get_taxonomy( $taxonomy_name );
			if ( !$taxonomy->show_ui )
				continue;

			if ( $taxonomy->hierarchical )
				$hierarchical_taxonomies[] = $taxonomy;
			else
				$flat_taxonomies[] = $taxonomy;
		}
		$this->h_taxonomies = $hierarchical_taxonomies;
		$this->f_taxonomies = $flat_taxonomies;
	}


	/**
	* Get Post Hierarchical Taxonomies
	*/
	private function hierarchicalTaxonomies($page_id)
	{
		$out = '';
		if ( count($this->h_taxonomies) > 0 ) {
			foreach ( $this->h_taxonomies as $taxonomy ){
				$terms = wp_get_post_terms($page_id, $taxonomy->name);
				foreach ( $terms as $term ){
					$out .= 'in-' . $taxonomy->name . '-' . $term->term_id . ' ';
				}
			}
		}
		return $out;
	}


	/**
	* Get Post Flat Taxonomies
	*/
	private function flatTaxonomies($page_id)
	{
		$out = '';
		if ( count($this->f_taxonomies) > 0 ) {
			foreach ( $this->f_taxonomies as $taxonomy ){
				$terms = wp_get_post_terms($page_id, $taxonomy->name);
				foreach ( $terms as $term ){
					$out .= 'inf-' . $taxonomy->name . '-nps-' . $term->term_id . ' ';
				}
			}
		}
		return $out;
	}


	/**
	* Opening list tag <ol>
	* @param array $pages - array of page objects from current query
	* @param int $count - current count in loop
	*/
	private function listOpening($pages, $count)
	{
		// Get array of child pages
		$children = array();
		$all_children = $pages->posts;
		foreach($all_children as $child){
			array_push($children, $child->ID);
		}
		// Compare child pages with user's toggled pages
		$compared = array_intersect($this->visiblePages(), $children);

		if ( $count == 1 ) {
			echo ( $this->user->canSortPages() ) 
				? '<ol class="sortable nplist visible">' 
				: '<ol class="sortable no-sort nplist" visible>';
		} else {
			echo '<ol class="nplist';
			if ( count($compared) > 0 ) echo ' visible" style="display:block;';
			echo '">';	
		} 
	}


	/**
	* Set Post Data
	*/
	private function setPostData($post)
	{
		$this->post_data['template'] = get_post_meta($post->ID, '_wp_page_template', true);

		// Show Hide in generated nav menu
		$ns = get_post_meta( get_the_id(), 'np_nav_status', true);
		$this->post_data['nav_status'] = ( $ns == 'hide' ) ? 'hide' : 'show';

		// Hidden in Nested Pages?
		$np_status = get_post_meta( get_the_id(), 'nested_pages_status', true );
		$this->post_data['np_status'] = ( $np_status == 'hide' ) ? 'hide' : 'show';

		// Menu Title
		$this->post_data['nav_title'] = get_post_meta(get_the_id(), 'np_nav_title', true);

		// Redirect Link Target
		$this->post_data['link_target'] = get_post_meta(get_the_id(), 'np_link_target', true);

		// Parent ID
		$this->post_data['parent_id'] = $post->post_parent;

		// Nav Title Attribute
		$this->post_data['nav_title_attr'] = get_post_meta(get_the_id(), 'np_title_attribute', true);

		// Nav CSS Classes
		$this->post_data['nav_css'] = get_post_meta(get_the_id(), 'np_nav_css_classes', true);

		// Post Password
		$this->post_data['password'] = $post->post_password;

		// Yoast Score
		if ( function_exists('wpseo_translate_score') ) {
			$yoast_score = get_post_meta(get_the_id(), '_yoast_wpseo_linkdex', true);
			$this->post_data['score'] = wpseo_translate_score($yoast_score);
		};

		// Date Vars
		$this->post_data['d'] = get_the_time('d');
		$this->post_data['month'] = get_the_time('m');
		$this->post_data['y'] = get_the_time('Y');
		$this->post_data['h'] = get_the_time('H');
		$this->post_data['m'] = get_the_time('i');
	}


	/**
	* Get count of published posts
	* @param object $pages
	*/
	private function publishCount($pages)
	{
		$publish_count = 1;
		foreach ( $pages->posts as $p ){
			if ( $p->post_status !== 'trash' ) $publish_count++;
		}
		return $publish_count;
	}


	/**
	* Loop through all the pages and create the nested / sortable list
	* Recursive Method, called in page.php view
	*/
	private function loopPages($parent_id = 0, $count = 0)
	{
		$this->setTaxonomies();
		$query_args = array(
			'post_type' => array('page','np-redirect'),
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'post_status' => array('publish', 'pending', 'draft', 'private', 'future', 'trash'),
			'post_parent' => $parent_id,
			'order' => 'ASC'
		);
		$pages = new \WP_Query(apply_filters('nestedpages_page_listing', $query_args));
		
		if ( $pages->have_posts() ) :
			$count++;

			if ( $this->publishCount($pages) > 1 ){
				$this->listOpening($pages, $count);			
			}
			
			while ( $pages->have_posts() ) : $pages->the_post();

				global $post;
				$this->setPostData($post);
				if ( get_post_status(get_the_id()) !== 'trash' ) :

					echo '<li id="menuItem_' . get_the_id() . '" class="page-row';

					// Published?
					if ( $post->post_status == 'publish' ) echo ' published';
					
					// Hidden in Nested Pages?
					if ( $this->post_data['np_status'] == 'hide' ) echo ' np-hide';

					// Taxonomies
					echo ' ' . $this->hierarchicalTaxonomies( get_the_id() );
					echo ' ' . $this->flatTaxonomies( get_the_id() );
					
					echo '">';
					
					$count++;

					if ( get_post_type() == 'page' ){
						include( Helpers::view('row') );
					} else {
						include( Helpers::view('row-redirect') );
					}

				endif; // trash status
				
				$this->loopPages(get_the_id(), $count);

				if ( get_post_status(get_the_id()) !== 'trash' ) {
					echo '</li>';
				}				

			endwhile; // Loop
			
			if ( $this->publishCount($pages) > 1 ){
				echo '</ol>';
			}

		endif; wp_reset_postdata();
	}


}