<?php 

namespace NestedPages\Config;
use NestedPages\Entities\PostType\PostTypeRepository;

class SettingsRepository 
{

	/**
	* Is the Datepicker UI option enabled
	* @return boolean
	*/
	public function datepickerEnabled()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['datepicker']) && $option['datepicker'] == 'true' ) return true;
		return false;
	}

	/**
	* Is the Menu Sync Option Visible
	*/
	public function hideMenuSync()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['hide_menu_sync']) && $option['hide_menu_sync'] == 'true' ) return true;
		return false;
	}

	/**
	* Is menu sync enabled?
	*/
	public function menuSyncEnabled()
	{
		$option = get_option('nestedpages_menusync');
		return ( $option == 'sync' ) ? true : false;
	}

	/**
	* Is AJAX menu sync disabled?
	*/
	public function autoMenuDisabled()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['manual_menu_sync']) && $option['manual_menu_sync'] == 'true' ) return true;
		return false;
	}

	/**
	* Is AJAX page order disabled?
	*/
	public function autoPageOrderDisabled()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['manual_page_order_sync']) && $option['manual_page_order_sync'] == 'true' ) return true;
		return false;
	}

	/**
	* Are menus completely disabled?
	* @return boolean
	*/
	public function menusDisabled()
	{
		$option = get_option('nestedpages_disable_menu');
		if ( $option && $option == 'true' ) return true;
		return false;
	}

	/**
	* Array of configurable standard fields
	* @return array
	*/
	public function standardFields($post_type)
	{
		$post_type_repo = new PostTypeRepository;

		$fields = array(
			'title' => __('Post Title', 'nestedpages'), 
			'slug' => __('Slug', 'nestedpages'), 
			'date' => __('Post Date', 'nestedpages'), 
			'author' => __('Author', 'nestedpages'),
			'status' => __('Post Status', 'nestedpages'),
			'password' => __('Password/Private', 'nestedpages'),
			'allow_comments' => __('Allow Comments', 'nestedpages')
		);

		if ( $post_type == 'page' ) {
			$fields['template'] = __('Template', 'nestedpages');
			$fields['menu_options'] = __('Menu Options', 'nestedpages');
		}

		$fields['hide_in_np'] = __('Hide in Nested Pages', 'nestedpages');
		
		// Taxonomies
		$enabled_h_taxonomies = $post_type_repo->getTaxonomies($post_type);
		$enabled_f_taxonomies = $post_type_repo->getTaxonomies($post_type, false);
		$enabled_taxonomies = array_merge($enabled_h_taxonomies, $enabled_f_taxonomies);
		if ( empty($enabled_taxonomies) ) return $fields;
		
		$fields['hide_taxonomies'] = __('Taxonomies', 'nestedpages');
		$fields['taxonomies'] = array();
		foreach($enabled_taxonomies as $taxonomy){
			$fields['taxonomies'][$taxonomy->name] = $taxonomy->labels->name;
		}

		return $fields;
	}

	/**
	* Get All Image Sizes Available in the theme
	*/
	public function getImageSizes()
	{
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}
		return $sizes;
	}

	/**
	* Get a specific image/thumbnail size
	*/
	public function getImageSize($size)
	{
		$sizes = $this->getImageSizes();
		if ( isset( $sizes[ $size ] ) ) return $sizes[ $size ];
		return false;
	}

}