<?php 
namespace NestedPages\Entities\PostType;

use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Post Types required by Nested Pages
*/
class RegisterPostTypes 
{
	/**
	* Plugin Integrations
	*/
	private $integrations;

	public function __construct()
	{
		$this->integrations = new IntegrationFactory;
		if ( $this->integrations->plugins->wpml->installed ) return;
		add_action( 'init', [ $this, 'registerRedirects'] );
	}

	/**
	* Redirects Post Type
	*/
	public function registerRedirects()
	{
		$labels = [
			'name' => __('Redirects', 'wp-nested-pages'),  
			'singular_name' => __('Redirect', 'wp-nested-pages'),
			'add_new_item'=> 'Add Redirect',
			'edit_item' => 'Edit Redirect',
			'view_item' => 'View Redirect'
		];
		$args = [
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'exclude_from_search' => true,
			'capability_type' => 'post',  
			'hierarchical' => true,  
			'has_archive' => false,
			'supports' => ['title','editor'],
			'_edit_link' => 'post.php?post=%d',
			'rewrite' => ['slug' => 'np-redirect', 'with_front' => false]
		];
		register_post_type( 'np-redirect' , $args );
	}
}