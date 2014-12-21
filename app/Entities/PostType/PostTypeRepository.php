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
		$types = get_post_types($args, $return);
		return $types;
	}


	/**
	* Get an object of non-page post types
	* @return object
	*/
	public function getPostTypesObject($pages = false)
	{
		$all_types = $this->getPostTypes('objects');
		$i = 0;
		foreach($all_types as $key => $type){
			if ( (!$pages) && ($key == 'page') ) continue;
			$post_types[$i] = new \stdClass();
			$post_types[$i]->name = $type->name;
			$post_types[$i]->label = $type->labels->name;
			$post_types[$i]->hierarchical = $type->hierarchical;
			$i++;
		}
		return $post_types;
	}

}