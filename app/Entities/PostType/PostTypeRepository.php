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
		$this->enabled_post_types = ( !$types ) ? [] : $types;
	}

	/**
	* Get an array of NP enabled Post Types
	* @since 1.2.1
	* @return array
	*/
	public function enabledPostTypes()
	{
		$types = get_option('nestedpages_posttypes');
		return ( !$types ) ? [] : $types;
	}

	/**
	* Get Available Post Types
	* @since 1.2.1
	* @return array
	*/
	public function getPostTypes($return = 'names')
	{
		return get_post_types(['show_ui'=>true], $return);
	}

	/**
	* Get an object of post types
	* @since 1.2.1
	* @return object
	*/
	public function getPostTypesObject()
	{
		$all_types = $this->getPostTypes('objects');
		$post_types = [];
		$enabled_types = $this->enabled_post_types;
		$invalid_types = [
			'acf-field-group',
			'attachment',
			'wp_block'
		];
		foreach($all_types as $key => $type){
			if ( in_array($type->name, $invalid_types) ) continue;
			$post_types[$type->name] = new \stdClass();
			$post_types[$type->name]->name = $type->name;
			$post_types[$type->name]->label = $type->labels->name;
			$post_types[$type->name]->hierarchical = $type->hierarchical;
			$post_types[$type->name]->np_enabled = ( array_key_exists($type->name, $this->enabled_post_types) ) ? true : false;
			$post_types[$type->name]->replace_menu = $this->postTypeSetting($type->name, 'replace_menu');
			$post_types[$type->name]->hide_default = $this->postTypeSetting($type->name, 'hide_default');
			$post_types[$type->name]->disable_sorting = $this->postTypeSetting($type->name, 'disable_sorting');
			$post_types[$type->name]->disable_nesting = $this->postTypeSetting($type->name, 'disable_nesting');
			$post_types[$type->name]->enable_max_nesting = $this->postTypeSetting($type->name, 'enable_max_nesting');
			$post_types[$type->name]->maximum_nesting = $this->postTypeSetting($type->name, 'maximum_nesting');
			$post_types[$type->name]->custom_fields_enabled = $this->postTypeSetting($type->name, 'custom_fields_enabled');
			$post_types[$type->name]->standard_fields_enabled = $this->postTypeSetting($type->name, 'standard_fields_enabled');
			$post_types[$type->name]->custom_fields = $this->configuredFields($type->name, 'custom_fields');
			$post_types[$type->name]->standard_fields = $this->configuredFields($type->name, 'standard_fields');
			$post_types[$type->name]->page_assignment = $this->configuredFields($type->name, 'post_type_page_assignment');
			$post_types[$type->name]->page_assignment_id = $this->configuredFields($type->name, 'post_type_page_assignment_page_id');
			$post_types[$type->name]->page_assignment_title = $this->configuredFields($type->name, 'post_type_page_assignment_page_title');
			$post_types[$type->name]->sort_options = $this->configuredFields($type->name, 'sort_options');
			$post_types[$type->name]->custom_statuses = $this->configuredFields($type->name, 'custom_statuses');
			$post_types[$type->name]->bulk_edit_roles = $this->configuredFields($type->name, 'bulk_edit_roles');
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
		$row_actions = [
			'wpml', 
			'comments', 
			'insert_before', 
			'insert_after', 
			'push_to_top', 
			'push_to_bottom', 
			'clone', 
			'quickedit', 
			'view', 
			'trash'
		];
		if ( $formatted_type->hierarchical ){
			$row_actions[] = 'add_child_link';
			$row_actions[] = 'add_child_page';
		}
		$filtered_row_actions = [];
		foreach ( $row_actions as $action ){
			if ( apply_filters("nestedpages_row_action_$action", true, $post_type) ) $filtered_row_actions[] = $action;
		}
		$formatted_type->row_actions = $filtered_row_actions;
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
				if ( $setting == 'true' ) return true;
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
		$fields = [];
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
	* Is a sort option enabled?
	* @param $post_type - post type name
	* @param $sort_option - option to search for
	* @param $taxonomy - boolean, is the sort option a taxonomy name
	* @return boolean
	*/
	public function sortOptionEnabled($post_type, $sort_option, $taxonomy = false)
	{
		$enabled = false;
		$options = $this->configuredFields($post_type, 'sort_options');
		if ( !is_array($options) ) return $enabled;
		if ( empty($options) ) return $enabled;
		foreach ( $options as $option => $value ){
			if ( $option == $sort_option && $value == 'true' ) $enabled = true;
			if ( $option == $sort_option && isset($value['enabled']) ) $enabled = true;
		}
		if ( $taxonomy && !isset($options['taxonomies']) ) $enabled = false;
		if ( $taxonomy && isset($options['taxonomies'][$sort_option]) && $options['taxonomies'][$sort_option] == 'true' ) $enabled = true;
		return $enabled;
	}

	/**
	* Is there a default option set for a sort parameter?
	* @param $post_type - post type name
	* @param $sort_option - option to search for
	*/
	public function defaultSortOption($post_type, $sort_option)
	{
		$enabled = false;
		$options = $this->configuredFields($post_type, 'sort_options');
		if ( !is_array($options) ) return $enabled;
		if ( empty($options) ) return $enabled;
		foreach ( $options as $option => $value ){
			if ( $option == $sort_option && isset($value['initial']) ) $enabled = $value['initial'];
			if ( $option == $sort_option && !isset($value['enabled']) ) $enabled = false;
		}
		return $enabled;
	}

	/**
	* Does the post type have any sort options
	* @return boolean
	*/
	public function hasSortOptions($post_type)
	{
		$options = $this->configuredFields($post_type, 'sort_options');
		if ( empty($options) ) return false;
		$enabled = false;
		foreach ( $options as $option ){
			if ( isset($option['enabled']) ) $enabled = true;
		}
		return $enabled;
	}

	/**
	* Does the user role have access to bulk edit
	* @return boolean
	*/
	public function roleCanBulkEdit($post_type, $role)
	{
		$option = $this->configuredFields($post_type, 'bulk_edit_roles');
		if ( !$option ) return true;
		if ( is_array($option) && empty($option) ) return true;
		return ( in_array($role, $option) ) ? true : false;
	}

	/**
	* Are Thumbnails enabled for this post type? If not, return false, if so, return the thumbnail size
	* @param $post_type - post type name
	* @return boolean || string (thumbnail size)
	*/
	public function thumbnails($post_type, $key = 'enabled')
	{
		$types = $this->enabled_post_types;
		$type_settings = [];
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
		$type_settings = [];
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
	* All Posts Link
	* @since 1.7.0
	* @param string post_type
	* @return string
	*/
	public function allPostsLink($post_type)
	{
		$pt_object = $this->getSinglePostType(esc_attr($post_type));
		if ( $pt_object->replace_menu ){
			return esc_url( admin_url('admin.php?page=nestedpages-' . $post_type) );
		}
		return esc_url( admin_url('edit.php?post_type=' . esc_attr($post_type)) );
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
		$hierarchical_taxonomies = [];
		$flat_taxonomies = [];
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
		$label = ( $post_type->hierarchical ) ? __('Nested View', 'wp-nested-pages') : __('Sort View', 'wp-nested-pages'); 
		return apply_filters('nestedpages_sortview_text', $label, $post_type);
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
		$fields = [];
		$exclude = ['_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time', 'layout', 'position', 'rule', 'hide_on_screen', '_np_link_target', '_np_nav_title', '_np_title_attribute', '_np_nav_status', '_nested_pages_status', '_np_nav_css_classes'];
		foreach ( $results as $field ){
			if ( !in_array($field->meta_key, $exclude) ) 
				array_push($fields, $field->meta_key);
		}
		return $fields;
	}

	/**
	* Get an array of assigned page IDs for all post types
	*/
	public function getAssignedPages()
	{
		$post_types = $this->getPostTypesObject();
		$array = [];
		foreach($post_types as $type => $options){
			if ( isset($options->page_assignment) && $options->page_assignment == 'true' && isset($options->page_assignment_id) && $options->page_assignment_id !== '' ) $array[$options->page_assignment_id] = $type;
		}
		return $array;
	}

	/**
	* Get quick edit post statuses for the post type
	*/
	public function quickEditStatuses($post_type)
	{
		$custom_statuses = ( isset($this->enabled_post_types[$post_type]['custom_statuses']) && !empty($this->enabled_post_types[$post_type]['custom_statuses']) ) ? $this->enabled_post_types[$post_type]['custom_statuses'] : null;
		$statuses = [
			'can_publish' => [
				'publish' => __('Published', 'wp-nested-pages'),
				'future' => __('Scheduled', 'wp-nested-pages'),
			],
			'other' => [
				'pending' => __('Pending Review', 'wp-nested-pages'),
				'draft' => __('Draft', 'wp-nested-pages'),
			]
		];
		if ( $custom_statuses ) :
			global $wp_post_statuses;
			foreach ( $custom_statuses as $custom_status ) {
				$statuses['other'][$custom_status] = $wp_post_statuses[$custom_status]->label;
			}
		endif;
		return apply_filters('nestedpages_quickedit_post_statuses', $statuses, $post_type);
	}
}