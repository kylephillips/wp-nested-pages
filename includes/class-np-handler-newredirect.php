<?php

function nestedpages_new_redirect()
{
	new NP_NewRedirect;
}

require_once('class-np-handler-base.php');
require_once('class-np-helpers.php');

/**
* Creates new Redirect/Link
* @return json response
*/
class NP_NewRedirect extends NP_BaseHandler {


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
		$updated = $this->post_repo->saveRedirect($this->data);
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
		$this->data['np_link_content'] = NP_Helpers::check_url($this->data['np_link_content']);
	}


	/**
	* Add additional data to the response object
	*/
	private function addData()
	{
		$this->data['nav_status'] = ( isset($this->data['nav_status']) ) ? 'hide' : 'show';
		$this->data['np_status'] = ( isset($this->data['nested_pages_status']) ) ? 'hide' : 'show';
		$this->data['link_target'] = ( isset($this->data['link_target']) ) ? '_blank' : 'none';
	}

}