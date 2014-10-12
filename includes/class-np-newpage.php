<?php
/**
* Performs Hooks to check for new page screen
* Select appropriate parent page if applicable & shows message
*/
class NP_NewPage {

	public function __construct()
	{
		add_action('admin_head', array($this, 'checkPage'));
	}


	/**
	* Check if the page is the add new page screen and there is a parameter to add a child page
	*/
	public function checkPage()
	{
		$page = get_current_screen();
		if ( ($page->id == 'page') && ($page->action == 'add') && (isset($_GET['npparent'])) ){
			$this->setParent();
		}
	}


	/**
	* Set the parent id & output script to select field value & show mesage
	*/
	private function setParent()
	{
		$parent = (int) sanitize_text_field($_GET['npparent']);
		$parent_title = get_the_title($parent);

		if ( $parent_title ) :
			$out = '<script>';
			$out .= 'jQuery(document).ready(function(){jQuery("#parent_id").val("' . $parent . '");var html = \'<div id="message" class="updated"><p>Adding child page under: <strong>' . get_the_title($parent) . '</strong>.</p></div>\'; jQuery(".wrap").prepend(html); });';
			$out .= '</script>';
			echo $out;
		endif;
	}

}
?>