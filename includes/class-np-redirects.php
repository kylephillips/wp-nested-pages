<?php 
/**
* Redirects in admin
*/
class NP_Redirects {


	public function __construct()
	{
		add_action('load-edit.php', array($this, 'pageTrashed'));
		add_action('load-edit.php', array($this, 'pageRestored'));
		add_action('load-edit.php', array($this, 'addNPLink'));
		add_filter( "views_edit-page", array($this, 'addNPLink' ));
	}


	/**
	* Redirect back to nested pages after pages moved to trashed
	*/
	public function pageTrashed()
	{	
		$screen = get_current_screen();
		if ( ($screen->id == 'edit-page') && (isset($_GET['trashed'])) && (intval($_GET['trashed']) >0)){
			$redirect = add_query_arg(array('page'=>'nestedpages', 'trashed' => true, 'ids' => $_GET['ids'] ));
			wp_redirect($redirect);
			exit();
		}
	}

	/**
	* Redirect to nested pages after page moved out of trash
	*/
	public function pageRestored()
	{
		$screen = get_current_screen();
		if ( ($screen->id == 'edit-page') && (isset($_GET['untrashed'])) && (intval($_GET['untrashed']) >0)){
			$redirect = add_query_arg(array('page'=>'nestedpages', 'untrashed' => true, 'untrashed' => $_GET['untrashed'] ));
			wp_redirect($redirect);
			exit();
		}
	}

	/**
	* Add a nested pages link to the subsub list (WP_List_Table class)
	*/
	public function addNPLink($views)
	{
		$screen = get_current_screen();
		if ( $screen->parent_file == 'edit.php?post_type=page' ){
			$link = array('Nested Pages' => '<a href="' . esc_url(admin_url('admin.php?page=nestedpages')) . '">Nested Pages</a>');
			$views = array_merge($views, $link);
		}
		return $views;
	}

}