<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\User\UserRepository;

/**
* Syncs User's Visible/Toggled Pages
*/
class NestToggle extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->updateUserMeta();
	}

	/**
	* Make sure this is an array of integers
	*/
	private function validateIDs()
	{
		if ( !is_array($this->data['ids']) ) $this->sendErrorResponse();
		foreach ($this->data['ids'] as $id){
			if ( !is_numeric($id) ) $this->sendErrorResponse();
		}
	}

	/**
	* Update the user meta with the array of IDs
	*/
	private function updateUserMeta()
	{
		$this->user->updateVisiblePages($this->data['posttype'], $this->data['ids']);
		$this->response = ['status'=>'success', 'data'=>$this->data];
		$this->sendResponse();
	}
}