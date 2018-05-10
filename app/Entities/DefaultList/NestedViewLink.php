<?php
namespace NestedPages\Entities\DefaultList;

use NestedPages\Entities\PostType\PostTypeRepository;

/**
* Adds "nested view/sort view" to default lists on enabled post types
*/
class NestedViewLink 
{
	/**
	* Post Type
	*/
	private $post_type;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;


	public function __construct($post_type)
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->post_type = $post_type;
		$this->addFilter();
	}

	/**
	* Add the WP Filter
	*/
	private function addFilter()
	{
		add_filter( 'views_edit-' . $this->post_type->name, [$this, 'addLink']);
	}

	/**
	* Add a nested pages link to the subsub list (WP_List_Table class)
	*/
	public function addLink($views)
	{
		$screen = get_current_screen();
		if ( $screen->parent_file == $this->post_type_repo->editSlug($this->post_type) ){
			$link_text = $this->post_type_repo->getSubmenuText($this->post_type);
			$link_href = esc_url(admin_url('admin.php?page=' . $this->post_type_repo->getMenuSlug($this->post_type)));
			$link = [$link_text => '<a href="' . $link_href . '">' . $link_text . '</a>'];
			$views = array_merge($views, $link);
		}
		return $views;
	}
}