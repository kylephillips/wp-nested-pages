<?php namespace NestedPages\Entities\PostType;

class PostTypeRepository {


	/**
	* Get Available Post Types
	* @return array
	*/
	public function getPostTypes($return = 'names')
	{
		$args = array(
			'public' => true,
			'show_ui' => true,
			'hierarchical' => true
		);
		return get_post_types($args, $return);
	}


	/**
	* Get an array of post types in name=>label format
	*/
	public function getPostTypeArray($pages = false)
	{
		$all_types = $this->getPostTypes('objects');
		$post_types = array();
		foreach($all_types as $key => $type){
			if ( (!$pages) && ($key == 'page') ) continue;
			$post_types[$key] = $type->labels->name;
		}
		return $post_types;
	}

}