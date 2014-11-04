<?php 
/**
* Redirects in admin
*/
class NP_Redirects {


	public function __construct()
	{
		add_action('load-edit.php', array($this, 'pageTrashed'));
	}


	/**
	* Redirect back to nested pages after a page is trashed
	* @todo - provide alert indicator on trash success
	*/
	public function pageTrashed()
	{
		$screen = get_current_screen();
		if ( ($screen->id == 'edit-page') && (isset($_GET['trashed'])) && (intval($_GET['trashed']) >0)){
			$redirect = add_query_arg(array('page'=>'nestedpages', 'trashed' => false, 'ids' => $_GET['ids'] ));
			wp_redirect($redirect);
			exit();
		}
	}

}