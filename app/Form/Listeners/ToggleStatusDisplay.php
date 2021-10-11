<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\User\UserRepository;

/**
* Save the user's status display preference
*/
class ToggleStatusDisplay extends BaseHandler
{
	/**
	* The Post Type
	* @var string
	*/
	private $post_type;

	/**
	* Post Type Repository
	* @var object
	*/
	private $post_type_repo;

	public function __construct()
	{
		parent::__construct();
		$this->updateStatus();
		$this->sendResponse();
	}

	private function updateStatus()
	{
		$status = sanitize_text_field($this->data['status']);
		$post_type = sanitize_text_field($this->data['post_type']);
		$this->user->updateStatusPreference($post_type, $status);
		$this->response = ['status'=>'success', 'message'=> __('Status preference updated.', 'wp-nested-pages')];
	}
}