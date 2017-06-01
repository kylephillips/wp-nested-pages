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
		$this->clonePost();
	}

	/**
	* Set the Post ID to Clone
	*/ 
	private function setPostID()
	{
		if ( !isset($_POST['parent_id']) ){
			return $this->sendResponse(array('status' => 'error', 'message' => __('Post Not Found', 'nestedapges')));
		}
		$this->data['post_id'] = intval(sanitize_text_field($_POST['parent_id']));
		$this->data['status'] = sanitize_text_field($_POST['status']);
		$this->data['author'] = intval(sanitize_text_field($_POST['author']));
		$this->data['quantity'] = intval(sanitize_text_field($_POST['quantity']));
		$this->data['post_type'] = sanitize_text_field($_POST['posttype']);
	}

	/**
	* Clone the post
	*/
	private function clonePost()
	{
		$new_posts = $this->cloner->clonePost($this->data['post_id'], $this->data['quantity'], $this->data['status'], $this->data['author']);
		return wp_send_json(array(
			'status' => 'success', 
			'posts' => $this->post_repo->postArray($new_posts, $this->data['post_type'])
		));
	}
}