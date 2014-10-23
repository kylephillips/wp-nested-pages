<?php

/**
* Primary Listing Class
* Initiates Page Listing screen (overwriting default), and displays primary plugin view.
*/
class NP_PageListing {

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


	public function __construct()
	{
		$this->post_type = get_post_type_object('page');
		add_action( 'admin_menu', array($this, 'adminMenu') );
		add_action( 'admin_menu', array($this, 'submenu') );
	}


	/**
	* Add the admin menu item
	*/
	public function adminMenu()
	{
		if ( current_user_can('edit_pages') ){
			add_menu_page( 
				$this->post_type->labels->name,
				$this->post_type->labels->name,
				'delete_pages',
				'nestedpages', 
				array( $this, 'pageListing' ),
				'dashicons-admin-page',
				20
			);
		}
	}


	/**
	* Add Submenu
	*/
	public function submenu()
	{
		global $submenu;
		$submenu['nestedpages'][50] = array( __('All Pages','nestedpages'), 'publish_pages', esc_url(admin_url('admin.php?page=nestedpages')) );
		$submenu['nestedpages'][60] = array( __('Add New','nestedpages'), 'publish_pages', $this->addNewPageLink() );
		$submenu['nestedpages'][70] = array( __('Default Pages','nestedpages'), 'publish_pages', $this->defaultPagesLink() );
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
	* Link to the default WP Pages listing
	* @return string
	*/
	private function defaultPagesLink()
	{
		$link = esc_url( admin_url('edit.php?post_type=page') );
		return $link;
	}


	/**
	* The Main View
	* Replaces Default Pages Listing
	*/
	public function pageListing()
	{
		include( dirname( dirname(__FILE__) ) . '/views/pages.php');
	}


	/**
	* Set the Taxonomies for Pages
	*/
	private function setTaxonomies()
	{
		$taxonomy_names = get_object_taxonomies( 'page' );
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
	* Get a Posts Taxonomies
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
		if ( $out !== '' ) $out .= ' has-tax ';
		return $out;
	}


	/**
	* Loop through all the pages and create the nested / sortable list
	* Recursive Method, called in page.php view
	*/
	private function loopPages($parent_id = 0, $count = 0)
	{
		$pages = new WP_Query(array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'post_parent' => $parent_id,
			'order' => 'ASC'
		));
		if ( $pages->have_posts() ) :
			$count++;

			if ( $count == 1 ) {
				
				$this->setTaxonomies();

				echo ( current_user_can('edit_theme_options') ) 
					? '<ol class="sortable nplist">' 
					: '<ol class="sortable no-sort nplist">';
			} else {
				echo '<ol class="nplist">';	
			} 

			while ( $pages->have_posts() ) : $pages->the_post();

				global $post;
				
				echo '<li id="menuItem_' . get_the_id() . '" class="page-row';

				// Published?
				if ( $post->post_status == 'publish' ) echo ' published';
				
				// Hidden in Nested Pages?
				$np_status = get_post_meta( get_the_id(), 'nested_pages_status', true );
				$np_status = ( $np_status == 'hide' ) ? 'hide' : 'show';
				if ( $np_status == 'hide' ) echo ' np-hide';

				// Taxonomies
				echo ' ' . $this->hierarchicalTaxonomies( get_the_id() );
				
				echo '">';
					$count++;
					
					$template = get_post_meta(get_the_id(), '_wp_page_template', true);
					
					// Show Hide in generated nav menu
					$ns = get_post_meta( get_the_id(), 'np_nav_status', true);
					$nav_status = ( $ns == 'hide' ) ? 'hide' : 'show';

					// Menu Title
					$nav_title = get_post_meta(get_the_id(), 'np_nav_title', true);
					
					// Date Vars
					$d = get_the_time('d');
					$month = get_the_time('m');
					$y = get_the_time('Y');
					$h = get_the_time('H');
					$m = get_the_time('i');

					if ( function_exists('wpseo_translate_score') ) {
						$yoast_score = get_post_meta(get_the_id(), '_yoast_wpseo_linkdex', true);
						$score = wpseo_translate_score($yoast_score);
					};
					
					include( dirname( dirname(__FILE__) ) . '/views/row.php');
				$this->loopPages(get_the_id(), $count);
				echo '</li>';

			endwhile; // Loop
			echo '</ol>';
		endif; wp_reset_postdata();
	}


}