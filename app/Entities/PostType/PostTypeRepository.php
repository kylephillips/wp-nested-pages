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

}