<?php namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\Listing\Listing;

/**
* Add a link to the default menu
*/
class AdminSubmenuDefault {

	/**
	* Post Type
	* @var object
	*/
	private $post_type;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;


	public function __construct($post_type)
	{
		$this->post_type = $post_type;
		$this->post_type_repo = new PostTypeRepository;
		$this->findMenu();
	}


	/**
	* Set the Submenu Text
	* "Nested View" for Hierarchical Post Types
	* "Sort View" for Non-Hierarchical Post Types
	*/
	private function getSubmenuText()
	{
		return ( $this->post_type->hierarchical ) ? __('Nested View', 'nestedpages') : __('Sort View', 'nestedpages');
	}


	/**
	* Add the submenu
	*/
	public function findMenu()
	{
		global $submenu;
		foreach($submenu as $key => $sub){
			if ($key == $this->post_type_repo->editSlug($this->post_type)){
				$this->addSubMenu($key);
			}
		}
	}


	/**
	* Add the submenu item
	* @param string parent page slug
	*/
	private function addSubMenu($parent_slug)
	{
		add_submenu_page( 
			$parent_slug,
			$this->getSubmenuText(),
			$this->getSubmenuText(),
			'edit_posts',
			$this->post_type_repo->getMenuSlug($this->post_type),
			Listing::admin_menu($this->post_type->name)
		);
	}

}