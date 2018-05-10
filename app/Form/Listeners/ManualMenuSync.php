<?php 
namespace NestedPages\Form\Listeners;

/**
* Manually Sync the Menu
* @return json response
*/
class ManualMenuSync extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->syncMenu();
		$this->response = ['status' => 'success'];
		$this->sendResponse();
	}
}