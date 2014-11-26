<?php
/**
* Performs Hooks to check for new page screen
* Select appropriate parent page if applicable & shows message
*/
class NP_NewPage {

	/**
	* Parent Page
	*/
	private $parent_page;


	public function __construct()
	{
		add_action('admin_notices', array($this, 'showNotice'));
		add_action('admin_head', array($this, 'selectParent'));
		add_action('admin_head', array($this, 'expandPagesMenu'));
	}


	/**
	* Check if this is a new child page
	*/
	private function isChild()
	{
		$page = get_current_screen();
		if ( ($page->id == 'page') && ($page->action == 'add') && (isset($_GET['npparent'])) ){
			$this->parent_page = (int) sanitize_text_field($_GET['npparent']);
			return true;
		} else {
			return false;
		}
	}


	/**
	* Show the admin notice
	*/
	public function showNotice()
	{
		if ( $this->isChild() ) {
			echo '<div id="message" class="updated"><p>' . __('Adding child page under:', 'nestedpages') . ' <strong>' . get_the_title($this->parent_page) . '</strong></p></div>';
		}
	}


	/**
	* Preselect the parent page
	*/
	public function selectParent()
	{
		if ( $this->isChild() ) {
			echo '<script>jQuery(document).ready(function(){ jQuery("#parent_id").val("' . $this->parent_page . '"); });</script>';
		}
	}

	/**
	* Expand the Pages submenu
	*/
	public function expandPagesMenu()
	{
		$page = get_current_screen();
		if ( ($page->id == 'page') && ($page->action == 'add') ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(3)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
		if ( $page->id == 'edit-page' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(4)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
		if ( $page->id == 'toplevel_page_nestedpages' ){
			echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_nestedpages").removeClass("wp-not-current-submenu").addClass("wp-has-current-submenu").addClass("wp-menu-open");jQuery("#toplevel_page_nestedpages a:first").addClass("wp-has-current-submenu");var addnew = jQuery("#toplevel_page_nestedpages ul li:nth-child(2)");jQuery(addnew).addClass("current");jQuery(addnew).children("a").addClass("current");});</script>';
		}
	}

}