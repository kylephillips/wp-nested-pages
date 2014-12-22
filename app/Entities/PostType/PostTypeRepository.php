<?php namespace NestedPages\Entities\PostType;

class PostTypeRepository {


	/**
	* Get Available Post Types
	* @since 1.2.1
	* @return array
	*/
	public function getPostTypes($return = 'names')
	{
		$args = array(
			'public' => true,
			'show_ui' => true
		);
		return get_post_types($args, $return);
	}


	/**
	* Get an object of non-page post types
	* @since 1.2.1
	* @return object
	*/
	public function getPostTypesObject($pages = false)
	{
		$all_types = $this->getPostTypes('objects');
		$post_types = array();
		$i = 0;
		foreach($all_types as $key => $type){
			if ( (!$pages) && ($key == 'attachment') ) continue;
			$post_types[$i] = new \stdClass();
			$post_types[$i]->name = $type->name;
			$post_types[$i]->label = $type->labels->name;
			$post_types[$i]->hierarchical = $type->hierarchical;
			$post_types[$i]->np_enabled = ( in_array($type->name, $this->enabledPostTypes()) ) ? true : false;
			$i++;
		}
		return $post_types;
	}


	/**
	* Get an array of NP enabled Post Types
	* @since 1.2.1
	* @return array
	*/
	public function enabledPostTypes()
	{
		$types = get_option('nestedpages_posttypes');
		if ( !$types ) $types = array();
		return $types;
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
			if ( !$taxonomy->show_ui )
				continue;

			if ( $taxonomy->hierarchical )
				$hierarchical_taxonomies[] = $taxonomy;
			else
				$flat_taxonomies[] = $taxonomy;
		}
		return ($hierarchical) ? $hierarchical_taxonomies : $flat_taxonomies;
	}


	/**
	* Get the NP menu slug for a post type
	* @param object WP Post Type Object
	*/
	public function getMenuSlug($post_type)
	{
		return ( $post_type->name == 'page' ) ? 'nestedpages' : 'nestedpages-' . $post_type->name;
	}





}