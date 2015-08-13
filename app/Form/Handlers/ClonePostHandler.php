<?php 

namespace NestedPages\Form\Handlers;

use NestedPages\Entities\Post\PostCloner;

/**
* Clone an existing post
*/
class ClonePostHandler extends BaseHandler
{
	/**
	* Post ID to Clone
	*/
	private $post_id;

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
		$this->post_id = intval(sanitize_text_field($_POST['parent_id']));
	}

	/**
	* Clone the post
	*/
	private function clonePost()
	{
		$new_post = $this->cloner->clonePost($this->post_id);
		return wp_send_json(array('status' => 'success', 'post' => $new_post));
	}
}