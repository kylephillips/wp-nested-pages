<?php 
namespace NestedPages\Form\Listeners;

class ResetSettings extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->reset();
	}

	private function reset()
	{
		$this->settings->resetSettings();
		return wp_send_json(['status' => 'success']);
	}
}