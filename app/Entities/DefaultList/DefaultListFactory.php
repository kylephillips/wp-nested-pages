<?php namespace NestedPages\Entities\DefaultList;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\DefaultList\DefaultListLink;

/**
* Add the Nested Pages link to default table subsubsub
*/
class DefaultListFactory {

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Post Type
	*/
	private $post_type;

	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->addDefaultLinks();
	}

	/**
	* Loop through Post Types & add link to activated types
	*/
	private function addDefaultLinks()
	{
		foreach($this->post_type_repo->getPostTypesObject() as $type){
			if ( $type->np_enabled ){
				new DefaultListLink($type);
			}
		}
	}

}