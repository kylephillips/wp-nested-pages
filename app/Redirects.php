<?php namespace NestedPages;
/**
* Page Redirects in admin
*/
class Redirects {


	public function __construct()
	{
		add_action('load-edit.php', array($this, 'pageTrashed'));
		add_action('load-edit.php', array($this, 'pageRestored'));
		add_action('deleted_post', array($this, 'linkDeleted'), 10, 1);
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
	* Add link trashed param to URL after delete (for notification)
	*/
	public function linkDeleted($post_id)
	{
		$screen = get_current_screen();
		if ( (get_post_type($post_id) == 'np-redirect') && ($screen->id == 'np-redirect') ){
			$redirect = add_query_arg(array('page'=>'nestedpages', 'linkdeleted' => true, '_wpnonce' => false, 'post' => false, 'action'=>false));
			wp_redirect($redirect);
			exit();
		}
	}


	

}