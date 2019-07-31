<?php 
namespace NestedPages\Form\Listeners;

class ResetAdminMenuSettings extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->reset();
	}

	private function reset()
	{
		$this->settings->resetAdminMenuSettings();
		return wp_send_json(['status' => 'success']);
	}
}