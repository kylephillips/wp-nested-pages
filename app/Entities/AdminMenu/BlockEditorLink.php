<?php
namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\User\UserRepository;

/**
* Replace the "Pages" link in the block editor if the plugin is set to override the menu link
*/
class BlockEditorLink
{
	/**
	* Current Admin Page/Screen Object
	*/
	private $page;

	/**
	* User Repository
	*/
	private $user;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->user = new UserRepository;
		$this->page = get_current_screen();
		if ( !$this->page ) return;
		$this->loopPostTypes();
	}

	/**
	* Loop through all the enabled post types
	* Skip if "Replace Menu" is not enabled
	*/
	private function loopPostTypes()
	{
		foreach($this->post_type_repo->getPostTypesObject() as $type){
			if ( !$type->replace_menu ) continue;
			$this->fullPageEditorLink($type);
		}
	}

	/**
	* Full Page Editor Link back to parent listing
	*/
	private function fullPageEditorLink($type)
	{
		if ( $this->page->id !== $type->name ) return;
		$user_can_view = apply_filters("nestedpages_sort_view_$type->name", $this->user->canViewSorting($type->name), $this->user->getRoles());
		if ( !$user_can_view ) return;
		$link = ( $type->name == 'page' ) ? 'admin.php?page=nestedpages' : 'admin.php?page=' . $this->post_type_repo->getMenuSlug($type);
		$url = admin_url($link);
		echo '<script>jQuery(window).on("load", function(){ var headerLink = jQuery(".edit-post-header .edit-post-fullscreen-mode-close"); jQuery(headerLink).attr("href", "' . $url . '"); });</script>';
	}
}