<?php
namespace NestedPages\Form\Listeners;

/**
* Handles processing the quick edit form for redirects
* @return json response
*/
class QuickEditLink extends BaseHandler
{
	public function __construct()
	{
		parent::__construct();
		$this->updatePost();
		$this->syncMenu();
		$this->sendResponse();
	}

	/**
	* Update the Post
	*/
	private function updatePost()
	{
		$updated = $this->post_update_repo->updateRedirect($this->data);
		if ( !$updated ) $this->sendErrorResponse();
		$this->addData();
		$this->response = [
			'status' => 'success',
			'message' => __('Link successfully updated.', 'wp-nested-pages'),
			'post_data' => $this->data
		];
	}

	/**
	* Add additional data to the response object
	*/
	private function addData()
	{
		$this->data['nav_status'] = ( isset($this->data['nav_status']) && $this->data['nav_status'] == 'hide' ) ? 'hide' : 'show';
		$this->data['np_status'] = ( isset($this->data['nested_pages_status']) ) ? 'hide' : 'show';
		$this->data['linkTarget'] = ( isset($this->data['linkTarget']) ) ? '_blank' : 'none';
	}
}