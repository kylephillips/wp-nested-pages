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

}
?>