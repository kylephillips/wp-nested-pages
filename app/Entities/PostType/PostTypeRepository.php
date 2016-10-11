<?php 

namespace NestedPages\Entities\PostType;

class PostTypeRepository 
{

	/**
	* Enabled Post Types
	* @var array – the posttype setting
	*/
	public $enabled_post_types;

	public function __construct()
	{
		$this->setEnabledPostTypes();
	}

	/**
	* Set an array of NP enabled Post Types
	* @since 1.2.1
	* @return array
	*/
	public function setEnabledPostTypes()
	{
		$types = get_option('nestedpages_posttypes');
		$this->enabled_post_types = ( !$types ) ? array() : $types;
	}

	/**
	* Get an array of NP enabled Post Types
	* @since 1.2.1
	* @return array
	*/
	public function enabledPostTypes()
	{
		$types = get_option('nestedpages_posttypes');
		return ( !$types ) ? array() : $types;
	}

	/**
	* Get Available Post Types
	* @since 1.2.1
	* @return array
	*/
	public function getPostTypes($return = 'names')
	{
		return get_post_types(array('show_ui'=>true), $return);
	}

	/**
	* Get an object of post types
	* @since 1.2.1
	* @return object
	*/
	public function getPostTypesObject()
	{
		$all_types = $this->getPostTypes('objects');
		$post_types = array();
		$enabled_types = $this->enabled_post_types;
		$invalid_types = array(
			'acf-field-group',
			'attachment'
		);
		foreach($all_types as $key => $type){
			if ( in_array($type->name, $invalid_types) ) continue;
			$post_types[$type->name] = new \stdClass();
			$post_types[$type->name]->name = $type->name;
			$post_types[$type->name]->label = $type->labels->name;
			$post_types[$type->name]->hierarchical = $type->hierarchical;
			$post_types[$type->name]->np_enabled = ( array_key_exists($type->name, $this->enabled_post_types) ) ? true : false;
			$post_types[$type->name]->replace_menu = $this->postTypeSetting($type->name, 'replace_menu');
			$post_types[$type->name]->hide_default = $this->postTypeSetting($type->name, 'hide_default');
			$post_types[$type->name]->disable_nesting = $this->postTypeSetting($type->name, 'disable_nesting');
			$post_types[$type->name]->custom_fields_enabled = $this->postTypeSetting($type->name, 'custom_fields_enabled');
			$post_types[$type->name]->standard_fields_enabled = $this->postTypeSetting($type->name, 'standard_fields_enabled');
			$post_types[$type->name]->custom_fields = $this->configuredFields($type->name, 'custom_fields');
			$post_types[$type->name]->standard_fields = $this->configuredFields($type->name, 'standard_fields');
		}
		return $post_types;
	}

	/**
	* Get a Single Enabled Post Type Object with settings
	* @param $post_type string - post type name
	*/
	public function getSinglePostType($post_type)
	{
		$all_types = $this->getPostTypesObject();
		$formatted_type = false;
		foreach ( $all_types as $type ){
			if ( $type->name == $post_type ) $formatted_type = $type;
		}
		return $formatted_type;
	}

	/**
	* Get a Post Type Boolean Setting
	* @param string $post_type post type name
	* @param string $setting_key option key
	* @return boolean
	*/
	public function postTypeSetting($post_type, $setting_key)
	{
		foreach($this->enabled_post_types as $key => $type_settings){
			if ( $key !== $post_type ) continue;
			if ( !is_array($type_settings) ) return false;
			foreach ( $type_settings as $option_key => $setting ){
				if ( $option_key !== $setting_key ) continue;
				return $setting;
			}
		}
		return false;
	}

	/**
	* Fields configured for a specific post type
	* @param string post type name
	* @param string field type (custom_fields|standard_fields)
	* @return array
	*/
	public function configuredFields($post_type, $field_type = 'custom_fields')
	{
		$fields = array();
		foreach($this->enabled_post_types as $key => $type){
			if ( $key == $post_type ){
				if ( isset($type[$field_type]) ) $fields = $type[$field_type];
			}
		}
		return $fields;
	}

	/**
	* Is a custom field enabled?
	* @param $post_type - post type name
	* @param $field_group - key for field group (acf, other, etc)
	* @param $field_key - field key to search for
	* @return boolean
	*/
	public function fieldEnabled($post_type, $field_group, $field_key, $field_type = 'custom_fields')
	{
		$enabled = false;
		$fields = $this->configuredFields($post_type, $field_type);
		if ( !is_array($fields) ) return $enabled;
		foreach ( $fields as $group => $fields ){
			if ( $group !== $field_group ) continue;
			foreach ( $fields as $key => $type ){
				if ( $key == $field_key ) $enabled = true;
			}
		}
		return $enabled;
	}

	/**
	* Are Thumbnails enabled for this post type? If not, return false, if so, return the thumbnail size
	* @param $post_type - post type name
	* @return boolean || string (thumbnail size)
	*/
	public function thumbnails($post_type, $key = 'enabled')
	{
		$types = $this->enabled_post_types;
		$type_settings = array();
		foreach ( $types as $type => $settings ){
			if ( $type !== $post_type ) continue;
			$type_settings = $settings;
		}
		if ( !is_array($type_settings) ) return false;
		if ( !array_key_exists('thumbnails', $type_settings) ) return false;
		if ( !isset($type_settings['thumbnails']['display']) ) return false;
		if ( $key == 'enabled' ) return true;
		if ( $key == 'source' ){
			return ( isset($type_settings['thumbnails']['size']) ) ? $type_settings['thumbnails']['size'] : 'thumbnail';
		}
		if ( $key == 'display_size' ){
			return ( isset($type_settings['thumbnails']['display_size']) ) ? $type_settings['thumbnails']['display_size'] : 'medium';
		}
	}

	/**
	* Thumbnail Display Size
	* @return string
	*/
	public function thumbnailDisplaySize($post_type)
	{
		$types = $this->enabled_post_types;
		$type_settings = array();
		foreach ( $types as $type => $settings ){
			if ( $type !== $post_type ) continue;
			$type_settings = $settings;
		}
	}

	/**
	* Is a standard field disabled in Quick Edit?
	* @param $field - The field key (title, slug, date, author, etc…)
	* @param $post_type - The post type
	* @return boolean
	*/
	public function standardFieldDisabled($field, $post_type)
	{
		$disabled = false;
		$fields = $this->configuredFields($post_type, 'standard_fields');
		if ( !isset($fields['standard']) ) return false;
		foreach ( $fields['standard'] as $key => $value ){
			if ( $key == $field && $value == 'true') return true;
		}
		return false;
	}

	/**
	* Is a taxonomy disabled in Quick Edit?
	* @param $taxonomy - The taxonomy Name
	* @param $post_type - The post type
	* @return boolean
	*/
	public function taxonomyDisabled($taxonomy, $post_type)
	{
		$disabled = false;
		$fields = $this->configuredFields($post_type, 'standard_fields');
		if ( empty($fields) ) return false;
		if ( !array_key_exists('taxonomies', $fields['standard']) ) return false;
		if ( !is_array($fields['standard']['taxonomies']) ) return false;
		foreach ( $fields['standard']['taxonomies'] as $tax => $value ){
			if ( $tax == $taxonomy && $value == 'true') return true;
		}
		return false;
	}

	/**
	* Add New Post Link
	* @since 1.2.1
	* @param string post_type
	* @return string
	*/
	public function addNewPostLink($post_type)
	{
		return esc_url( admin_url('post-new.php?post_type=' . $post_type) );
	}

	/**
	* Trash Link
	* @since 1.2.1
	* @param string post_type
	* @return string
	*/
	public function trashLink($post_type)
	{
		return esc_url( admin_url('edit.php?post_status=trash&post_type=' . $post_type) );
	}

	/**
	* Edit Post Link
	* @since 1.2.1
	* @param string post_type
	* @return string
	*/
	public function editSlug($post_type)
	{
		return ( $post_type->name == 'post' ) ? 'edit.php' : 'edit.php?post_type=' . $post_type->name;
	}

	/**
	* Get Taxonomies enabled for post type
	* @since 1.2.1
	* @return array of taxonomy objects
	* @param string post_type name
	* @param boolean hierarchical
	*/
	public function getTaxonomies($post_type, $hierarchical = true)
	{
		$taxonomy_names = get_object_taxonomies( $post_type );
		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( $taxonomy_names as $taxonomy_name ) {
			$taxonomy = get_taxonomy( $taxonomy_name );
			if ( !$taxonomy->show_ui )continue;

			if ( $taxonomy->hierarchical )
				$hierarchical_taxonomies[] = $taxonomy;
			else
				$flat_taxonomies[] = $taxonomy;
		}
		return ($hierarchical) ? $hierarchical_taxonomies : $flat_taxonomies;
	}

	/**
	* Are Categories Enabled for a post type?
	* @since 1.5.0
	* @return boolean
	*/
	public function categoriesEnabled($post_type)
	{
		$taxonomies = $this->getTaxonomies($post_type, true);
		$enabled = false;
		foreach($taxonomies as $taxonomy){
			if ( $taxonomy->name == 'category' ) $enabled = true;
		}
		return $enabled;
	}

	/**
	* Get the NP menu slug for a post type
	* @param object WP Post Type Object
	*/
	public function getMenuSlug($post_type)
	{
		return ( $post_type->name == 'page' ) ? 'nestedpages' : 'nestedpages-' . $post_type->name;
	}

	/**
	* Set the Submenu Text
	* "Nested View" for Hierarchical Post Types
	* "Sort View" for Non-Hierarchical Post Types
	*/
	public function getSubmenuText($post_type)
	{
		return ( $post_type->hierarchical ) ? __('Nested View', 'nestedpages') : __('Sort View', 'nestedpages');
	}

	/**
	* Get all custom fields associated with a post type
	* @param string post_type
	* @param boolean show_hidden
	* @return array
	*/
	public function getCustomFields($post_type, $show_hidden = false)
	{
		global $wpdb;
		$post_table = $wpdb->prefix . 'posts';
		$meta_table = $wpdb->prefix . 'postmeta';
		if ( $show_hidden ){
			$sql = "SELECT DISTINCT meta_key FROM $post_table AS p LEFT JOIN $meta_table AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE ''";
		} else {
			$sql = "SELECT DISTINCT meta_key FROM $post_table AS p LEFT JOIN $meta_table AS m ON m.post_id = p.id WHERE p.post_type = '$post_type' AND meta_key NOT LIKE '\_%'";
		}
		$results = $wpdb->get_results($sql);
		$fields = ( $results ) ? $this->fieldsArray($results) : array();
		return $fields;
	}

	/**
	* Format DB results into an array
	*/
	private function fieldsArray($results)
	{
		$fields = array();
		$exclude = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen', 'np_link_target', 'np_nav_title', 'np_title_attribute', 'np_nav_status', 'nested_pages_status', 'np_nav_css_classes');
		foreach ( $results as $field ){
			if ( !in_array($field->meta_key, $exclude) ) 
				array_push($fields, $field->meta_key);
		}
		return $fields;
	}

}