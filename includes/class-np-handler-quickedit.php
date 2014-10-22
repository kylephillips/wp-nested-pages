<?php

function nestedpages_quickedit_handler()
{
	new NP_QuickEdit_Handler;
}

require_once('class-np-handler-base.php');

/**
* Handles processing the quick edit form
* @return json response
*/
class NP_QuickEdit_Handler extends NP_BaseHandler {


	public function __construct()
	{
		parent::__construct();
		$this->updatePost();
		$this->syncMenu();
		$this->sendResponse();
	}


	/**
	* Update the Post
	* @todo update taxonomies
	*/
	private function updatePost()
	{
		$updated = $this->post_repo->updatePost($this->data);
		if ( !$updated ) $this->sendErrorResponse();
		$this->addData();
		$this->response = array(
			'status' => 'success', 
			'message' => __('Post successfully updated'), 
			'post_data' => $this->data
		);
	}


	/**
	* Add additional data to the response object
	*/
	private function addData()
	{
		$this->data['nav_status'] = ( isset($this->data['nav_status']) ) ? 'hide' : 'show';
		$this->data['np_status'] = ( isset($this->data['nested_pages_status']) ) ? 'hide' : 'show';
		if ( !isset($_POST['comment_status']) ) $this->data['comment_status'] = 'closed';
	}

}