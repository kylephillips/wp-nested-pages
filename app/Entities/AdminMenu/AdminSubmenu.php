<?php namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\PostType\PostTypeRepository;

/**
* Adds the submenu for a given menu
*/
class AdminSubmenu {

	/**
	* Post Type
	* @var string
	*/
	private $post_type;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Slug
	* @var string
	*/
	private $slug;


	public function __construct($post_type)
	{
		$this->post_type = $post_type;
		$this->post_type_repo = new PostTypeRepository;
		$this->setSlug();
	}

	/**
	* Add the submenu
	*/
	public function addSubmenu()
	{
		global $submenu;

		// Get the right submenu and remove all pages link
		foreach($submenu as $key => $sub){
			if ($key == $this->post_type_repo->editSlug($this->post_type)){
				// Add the "All Link"
				$submenu[$this->slug][50] = array( $sub[5][0], 'publish_pages', esc_url(admin_url('admin.php?page=' . $this->slug)) );
				unset($sub['5']); // Remove Top Level
				$menu_items = $sub;
			}
		}

		if ( isset($menu_items) ){
			$c = 60;
			foreach($menu_items as $item){
				$submenu[$this->slug][$c] = array( $item[0], $item[1], $item[2]);
				$c = $c + 10;
			}
		}
		$this->defaultLink($c);
		
	}


	/**
	* Show the default link if set to show
	* @param int $c Menu Position Counter
	*/
	private function defaultLink($c)
	{
		global $submenu;
		if ( !$this->post_type_repo->hideDefault($this->post_type->name) ){
			$submenu[$this->slug][$c] = array( 
				__('Default','nestedpages') . ' ' . $this->post_type->labels->name, 
				'publish_pages', 
				$this->post_type_repo->editSlug($this->post_type)
			);
		}
	}


	/**
	* Set the Menu Slug
	*/
	private function setSlug()
	{
		$this->slug = $this->post_type_repo->getMenuSlug($this->post_type);
	}

}