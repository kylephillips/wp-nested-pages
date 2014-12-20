<?php namespace NestedPages\Controllers;
/**
* Opens the Submenu on child admin pages and highlights the current item
*/
class AdminSubmenuController {

	/**
	* Current Page Object
	*/
	private $page;

	public function __construct()
	{
		add_action('admin_head', array($this, 'expandPagesMenu'));
	}

	/**
	* Expand the Pages submenu
	*/
	public function expandPagesMenu()
	{
		$this->page = get_current_screen();
		$this->newPage();
		$this->editPage();
		$this->nestedPages();
	}

	/**
	* New Page Screen
	*/
	private function newPage()
	{
		if ( ($this->page->id == 'page') && ($this->page->action == 'add') ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(3)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}

	/**
	* Edit Page Screen
	*/
	private function editPage()
	{
		if ( $this->page->id == 'edit-page' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(4)");});</script>';
		}
	}

	/**
	* Nested Pages View
	*/
	private function nestedPages()
	{
		if ( $this->page->id == 'toplevel_page_nestedpages' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(2)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}


}