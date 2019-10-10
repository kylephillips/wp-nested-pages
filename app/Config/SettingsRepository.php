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
	* Is the Classic (non-indented) display option enabled
	* @return boolean
	*/
	public function nonIndentEnabled()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['non_indent']) && $option['non_indent'] == 'true' ) return true;
		return false;
	}
	

	/**
	* Is the Menu Sync Option Visible
	*/
	public function hideMenuSync()
	{
		$option = get_option('nestedpages_ui', false);
		$visible = ( $option && isset($option['hide_menu_sync']) && $option['hide_menu_sync'] == 'true' ) ? true : false;
		return apply_filters('nestedpages_menu_sync_visible', $visible);
	}

	/**
	* Is menu sync enabled?
	*/
	public function menuSyncEnabled()
	{
		$option = get_option('nestedpages_menusync');
		$enabled = ( $option == 'sync' ) ? true : false;
		return apply_filters('nestedpages_menu_sync_enabled', $enabled);
	}

	/**
	* Is AJAX menu sync disabled?
	*/
	public function autoMenuDisabled()
	{
		$option = get_option('nestedpages_ui', false);
		$enabled = ( $option && isset($option['manual_menu_sync']) && $option['manual_menu_sync'] == 'true' ) ? true : false;
		return apply_filters('nestedpages_menu_autosync_enabled', $enabled);
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
		$disabled = ( $option && $option == 'true' ) ? true : false;
		return apply_filters('nestedpages_menus_disabled', $disabled);
	}

	/**
	* Array of configurable standard fields
	* @return array
	*/
	public function standardFields($post_type)
	{
		$post_type_repo = new PostTypeRepository;

		$fields = [
			'title' => __('Post Title', 'wp-nested-pages'), 
			'slug' => __('Slug', 'wp-nested-pages'), 
			'date' => __('Post Date', 'wp-nested-pages'), 
			'author' => __('Author', 'wp-nested-pages'),
			'status' => __('Post Status', 'wp-nested-pages'),
			'password' => __('Password/Private', 'wp-nested-pages'),
			'allow_comments' => __('Allow Comments', 'wp-nested-pages')
		];

		if ( $post_type == 'page' ) {
			$fields['template'] = __('Template', 'wp-nested-pages');
			$fields['menu_options'] = __('Menu Options', 'wp-nested-pages');
		}

		$fields['hide_in_np'] = __('Hide in Nested Pages', 'wp-nested-pages');
		
		// Taxonomies
		$enabled_h_taxonomies = $post_type_repo->getTaxonomies($post_type);
		$enabled_f_taxonomies = $post_type_repo->getTaxonomies($post_type, false);
		$enabled_taxonomies = array_merge($enabled_h_taxonomies, $enabled_f_taxonomies);
		if ( empty($enabled_taxonomies) ) return $fields;
		
		$fields['hide_taxonomies'] = __('Taxonomies', 'wp-nested-pages');
		$fields['taxonomies'] = [];
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
		$sizes = [];
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, ['thumbnail', 'medium', 'medium_large', 'large'] ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = [
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				];
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

	/**
	* Admin Customization
	*/
	public function adminCustomEnabled($enabled)
	{
		$option = get_option('nestedpages_admin');
		if ( !isset($option[$enabled]) ) return false;
		return $option[$enabled];
	}

	/**
	* Hidden Menu Items
	*/
	public function adminMenuHidden($role = 'administrator')
	{
		$roles = $this->adminCustomEnabled('nav_menu_options');
		if ( !$roles ) return;
		$hidden = array();
		if ( !isset($roles[$role]) ) return;
		foreach($roles[$role] as $key => $options){
			if ( isset($options['hidden']) ) $hidden[] = $options['hidden'];
		}
		return $hidden;
	}

	/**
	* Reset all plugin settings
	*/
	public function resetSettings()
	{
		$options = [
			'nested_pages_custom_fields_hidden',
			'nestedpages_allowsorting',
			'nestedpages_disable_menu',
			'nestedpages_menu',
			'nestedpages_menusync',
			'nestedpages_posttypes',
			'nestedpages_ui',
			'nestedpages_version',
			'nestedpages_admin'
		];
		foreach($options as $option){
			delete_option($option);
		}
	}

	/**
	* Reset admin menu customizations
	*/
	public function resetAdminMenuSettings()
	{
		$options = [
			'nestedpages_admin'
		];
		foreach($options as $option){
			delete_option($option);
		}
	}

	/**
	* Get the Menu Name
	* @return term obj
	*/
	public function getMenuTerm()
	{
		$menu_id = get_option('nestedpages_menu');
		if ( !$menu_id ) return false;
		$term = ( is_numeric($menu_id) ) ? get_term_by('id', $menu_id, 'nav_menu') : false;
		return $term;
	}
}