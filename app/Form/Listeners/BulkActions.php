<?php 
namespace NestedPages\Form\Listeners;

/**
* Perform Bulk Actions
*/
class BulkActions 
{
	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	/**
	* Post IDs (Comma-separated)
	* @var string
	*/
	private $post_ids;

	/**
	* Post IDs to Perform Actions on, Formatted as an Array
	* @var array
	*/
	private $post_ids_array;

	/**
	* Redirect Post IDs (Comma-separated)
	* @var string
	*/
	private $redirect_post_ids;

	/**
	* Redirect Post IDs to Perform Actions on, Formatted as an Array
	* @var array
	*/
	private $redirect_post_ids_array;

	public function __construct()
	{
		$this->setURL();
		$this->setPostIds();
		$this->performAction();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
	}

	/**
	* Set the Post IDs
	*/
	private function setPostIds()
	{
		$ids = sanitize_text_field($_POST['post_ids']);
		$this->post_ids = rtrim($ids, ",");
		$ids = explode(',', $this->post_ids);
		$this->post_ids_array = $ids;

		$r_ids = sanitize_text_field($_POST['redirect_post_ids']);
		$this->redirect_post_ids = rtrim($r_ids, ",");
		$r_ids = explode(',', $this->redirect_post_ids);
		$this->redirect_post_ids_array = $r_ids;
	}

	/**
	* Perform the Bulk Actions
	*/
	private function performAction()
	{
		$action = sanitize_text_field($_POST['np_bulk_action']);

		if ( $action == 'no-action' || !$action ){
			$this->redirect();
			return;
		}

		if ( $action == 'trash' ){
			$this->trashPosts();
			return;
		}
	}

	/**
	* Move Posts to Trash
	*/
	private function trashPosts()
	{
		if ( !current_user_can('delete_pages') ){
			$this->redirect();
			return;
		}

		foreach ( $this->post_ids_array as $id ){
			wp_trash_post($id);
		}

		foreach ( $this->redirect_post_ids_array as $id ){
			wp_delete_post($id, true);
		}

		$this->url = $this->url . '&bulk=true&trashed=1';
		
		if ( $this->post_ids != '' ) $this->url = $this->url . '&ids=' . $this->post_ids;
		if ( $this->redirect_post_ids != '' ) $this->url = $this->url . '&link_ids=' . $this->redirect_post_ids;
	}

	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}