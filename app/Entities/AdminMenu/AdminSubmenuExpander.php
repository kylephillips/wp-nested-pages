<?php
namespace NestedPages\Entities\AdminMenu;

use NestedPages\Entities\PostType\PostTypeRepository;

/**
* Opens the Submenu on child admin pages and highlights the current item
*/
class AdminSubmenuExpander 
{
	/**
	* Current Page Object
	*/
	private $page;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;


	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->page = get_current_screen();
		if ( !$this->page ) return;
		$this->addHighlighting();
	}

	/**
	* Add Highlighting to enabled Post Types who's menus are being overwritten
	*/
	private function addHighlighting()
	{
		foreach($this->post_type_repo->getPostTypesObject() as $type){
			if ( $type->replace_menu ){
				$this->newPage($type);
				$this->editPage($type);
				$this->nestedPagesAll($type);
				$this->topLevelPostType($type);
				$this->defaultLink($type);
				$this->defaultPagesLink($type);
			}
		}
	}

	/**
	* New Page Screen
	*/
	private function newPage($type)
	{
		$name = ( $type->name == 'page' ) ? '' : '-' . $type->name;
		if ( ($this->page->id == $type->name) && ($this->page->action == 'add') ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages' . esc_html($name) . '").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages' . esc_html($name) . ' a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(3)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}

	/**
	* Edit Page Screen
	*/
	private function editPage($type)
	{
		$name = ( $type->name == 'page' ) ? '' : '-' . $type->name;
		if ( ($this->page->id == $type->name) && ($this->page->base == 'post') ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages' . esc_html($name) . '").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages' . esc_html($name) . ' a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li");jQuery(addnew).removeClass("current");jQuery(addnew).children("a").removeClass("current");});</script>';
		}
	}

	/**
	* Nested Pages View
	*/
	private function nestedPagesAll($type)
	{
		if ( $this->page->id == 'toplevel_page_nestedpages' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(2)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}

	/**
	* Top Level Page for Post Type
	*/
	private function topLevelPostType($type)
	{
		if ( $this->page->id == 'toplevel_page_nestedpages-' . esc_attr( $type->name ) ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages-' . esc_attr( $type->name ) . '").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages-' . esc_attr( $type->name ) . ' a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages-' . esc_attr( $type->name ) . ' ul li:nth-child(2)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}

	/**
	* Default Link
	*/
	private function defaultLink($type)
	{
		if ( $this->page->id == 'edit-' . $type->name ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages-' . esc_attr( $type->name ) . '").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages-' . esc_attr( $type->name ) . ' a:first").addClass("wp-has-current-submenu");});</script>';
		}
	}

	/**
	* Default Pages Link
	*/
	private function defaultPagesLink($type)
	{
		if ( $this->page->id == 'edit-page' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");});</script>';
		}
	}
}