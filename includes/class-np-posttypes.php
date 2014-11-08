<?php
require_once('class-np-navmenu.php');
/**
* Post Types required by Nested Pages
*/
class NP_PostTypes {

	public function __construct()
	{
		add_action( 'init', array( $this, 'registerRedirects') );
		add_action( 'trashed_post', array( $this, 'trashHook' ) );
	}


	/**
	* Redirects Post Type
	*/
	public function registerRedirects()
	{
		$labels = array(
			'name' => __('Redirects'),  
			'singular_name' => __('Redirect'),
			'add_new_item'=> 'Add Redirect',
			'edit_item' => 'Edit Redirect',
			'view_item' => 'View Redirect'
		);
		$args = array(
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'menu_position' => 5,
			'capability_type' => 'post',  
			'hierarchical' => true,  
			'has_archive' => true,
			'supports' => array('title','editor'),
			'rewrite' => array('slug' => 'np-redirect', 'with_front' => false)
		);
		register_post_type( 'np-redirect' , $args );
	}



	/**
	* Trash hook - unset parent pages
	*/
	public function trashHook($post_id)
	{
		$post_type = get_post_type($post_id);
		if ( $post_type == 'page' ) $this->resetToggles($post_id);
	}


	/**
	* Make sure children of trashed pages are viewable in Nested Pages
	*/
	private function resetToggles($post_id)
	{
		$visible_pages = unserialize(get_user_meta(get_current_user_id(), 'np_visible_pages', true));
		$child_pages = array();
		$children = new WP_Query(array('post_type'=>'page', 'posts_per_page'=>-1, 'post_parent'=>$post_id));
		if ( $children->have_posts() ) : while ( $children->have_posts() ) : $children->the_post();
			array_push($child_pages, get_the_id());
		endwhile; endif; wp_reset_postdata();
		foreach($child_pages as $child_page){
			if ( !in_array($child_page, $visible_pages) ) array_push($visible_pages, $child_page);
		}
		update_user_meta(get_current_user_id(), 'np_visible_pages', serialize($visible_pages));
	}

}