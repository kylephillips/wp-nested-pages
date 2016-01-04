<?php 

namespace NestedPages\Entities\PostType;

/**
* Post Types required by Nested Pages
*/
class RegisterPostTypes 
{

	public function __construct()
	{
		add_action( 'init', array( $this, 'registerRedirects') );
	}

	/**
	* Redirects Post Type
	*/
	public function registerRedirects()
	{
		$labels = array(
			'name' => __('Redirects', 'nestedpages'),  
			'singular_name' => __('Redirect', 'nestedpages'),
			'add_new_item'=> 'Add Redirect',
			'edit_item' => 'Edit Redirect',
			'view_item' => 'View Redirect'
		);
		$args = array(
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'exclude_from_search' => true,
			'capability_type' => 'post',  
			'hierarchical' => true,  
			'has_archive' => false,
			'supports' => array('title','editor'),
			'_edit_link' => 'post.php?post=%d',
			'rewrite' => array('slug' => 'np-redirect', 'with_front' => false)
		);
		register_post_type( 'np-redirect' , $args );
	}

}