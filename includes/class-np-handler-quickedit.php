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
		//return wp_send_json($this->data);
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
		$update = $this->post_repo->updatePost($this->data);
		if ( $update ){
			
			$data = $this->data;
			
			// Add additional meta to response
			$data['nav_status'] = ( isset($data['nav_status']) ) ? 'hide' : 'show';
			$data['np_status'] = ( isset($data['nested_pages_status']) ) ? 'hide' : 'show';

			if ( !isset($_POST['comment_status']) ) $data['comment_status'] = 'closed';

			$this->response = array(
				'status' => 'success', 
				'message' => __('Post successfully updated'), 
				'post_data' => $data
			);
		} else {
			$this->response = array(
				'status' => 'error', 
				'message' => __('There was an error updating the page.') 
			);
		}
	}

}