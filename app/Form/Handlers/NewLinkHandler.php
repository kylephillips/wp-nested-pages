<?php namespace NestedPages\Form\Handlers;

use NestedPages\Helpers;
/**
* Creates new Redirect/Link
* @return json response
*/
class NewLinkHandler extends BaseHandler {


	public function __construct()
	{
		parent::__construct();
		$this->saveRedirect();
		$this->syncMenu();
		$this->sendResponse();
	}


	/**
	* Update the Post
	* @todo update taxonomies
	*/
	private function saveRedirect()
	{
		$updated = $this->post_update_repo->saveRedirect($this->data);
		if ( !$updated ) $this->sendErrorResponse();
		$this->data['id'] = $updated;
		$this->addData();
		$this->formatLink();
		$this->response = array(
			'status' => 'success', 
			'message' => __('Link successfully updated', 'nestedpages'),
			'post_data' => $this->data
		);
	}


	/**
	* Format the new link for AJAX response
	*/
	private function formatLink()
	{
		$this->data['np_link_content'] = Helpers::check_url($this->data['np_link_content']);
	}


	/**
	* Add additional data to the response object
	*/
	private function addData()
	{
		$this->data['delete_link'] = get_delete_post_link($this->data['id'],'', true);
		$this->data['nav_status'] = ( isset($this->data['nav_status']) ) ? 'hide' : 'show';
		$this->data['np_status'] = ( isset($this->data['nested_pages_status']) ) ? 'hide' : 'show';
		$this->data['link_target'] = ( isset($this->data['link_target']) ) ? '_blank' : 'none';
	}

}