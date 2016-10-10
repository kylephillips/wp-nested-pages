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
	* Post IDs to Perform Actions on
	* @var array
	*/
	private $post_ids;

	public function __construct()
	{
		$this->setURL();
		$this->setPostIds();
		$this->performAction();
		// $this->redirect();
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
		$ids = rtrim($ids, ",");
		$ids = explode(',', $ids);
		$this->post_ids = $ids;
	}

	/**
	* Perform the Bulk Actions
	*/
	private function performAction()
	{
		$action = sanitize_text_field($_POST['np_bulk_action']);
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
		var_dump($this->post_ids);
	}


	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}