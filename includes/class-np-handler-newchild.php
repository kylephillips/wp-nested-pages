<?php

function nestedpages_newchild_handler()
{
	new NP_NewChild_Handler;
}

require_once('class-np-handler-base.php');
require_once('class-np-validation.php');
require_once('class-np-factory-post.php');

/**
* Handles processing the quick edit form
* @return json response
*/
class NP_NewChild_Handler extends NP_BaseHandler {

	/**
	* Post Factory
	*/
	private $factory;

	/**
	* Validation
	*/
	private $validation;


	public function __construct()
	{
		parent::__construct();
		$this->factory = new NP_PostFactory;
		$this->validation = new NP_Validation;
		$this->savePages();
		$this->syncMenu();
		$this->sendResponse();
	}


	/**
	* Run Validation
	*/
	private function validates()
	{
		return $this->validation->validateNewPages($this->data);
	}


	/**
	* Save the new page(s)
	*/
	private function savePages()
	{
		if ( $this->validates() ){
			$this->data['new_pages'] = $this->factory->createChildPages($this->data);
			$this->setResponse();
			return;
		}
		$this->sendErrorResponse();
	}


	/**
	* Set the Response
	*/
	private function setResponse()
	{
		$this->response = array(
			'status'=>'success',
			'new_pages' => $this->data['new_pages']
		);
	}

}

