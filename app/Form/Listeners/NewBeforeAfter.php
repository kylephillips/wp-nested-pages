<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Form\Validation\Validation;
use NestedPages\Entities\Post\PostFactory;

/**
* Adds posts before or after a specified post
* @return json response
*/
class NewBeforeAfter extends BaseHandler 
{
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
		$this->factory = new PostFactory;
		$this->validation = new Validation;
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
			$this->data['new_pages'] = $this->factory->createBeforeAfterPosts($this->data);
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
		$this->response = [
			'status'=>'success',
			'new_pages' => $this->data['new_pages']
		];
	}
}