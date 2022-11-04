<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\Post\PostCloner;

/**
* Clone an existing post
*/
class ClonePost extends BaseHandler
{
	/**
	* Post ID/status/author to Clone
	*/
	protected $data;

	/**
	* Cloner Object
	*/
	private $cloner;

	public function __construct()
	{
		parent::__construct();
		$this->cloner = new PostCloner;
		$this->setPostID();
		if ( !current_user_can('edit_post', $this->data['post_id']) ) return;
		$this->clonePost();
	}

	/**
	* Set the Post ID to Clone
	*/ 
	private function setPostID()
	{
		if ( !isset($_POST['parent_id']) ){
			return $this->sendResponse(['status' => 'error', 'message' => __('Post Not Found', 'wp-nested-pages')]);
		}
		$this->data['post_id'] = intval(sanitize_text_field($_POST['parent_id']));
		$this->data['status'] = sanitize_text_field($_POST['status']);
		$this->data['author'] = intval(sanitize_text_field($_POST['author']));
		$this->data['quantity'] = intval(sanitize_text_field($_POST['quantity']));
		$this->data['post_type'] = sanitize_text_field($_POST['posttype']);
		$this->data['clone_children'] = ( $_POST['clone_children'] == 'true' ) ? true : false;
	}

	/**
	* Clone the post
	*/
	private function clonePost()
	{
		$this->cloner->clonePost(
			$this->data['post_id'], 
			$this->data['quantity'], 
			$this->data['status'], 
			$this->data['author'],
			$this->data['clone_children']
		);
		return wp_send_json(['status' => 'success']);
	}
}