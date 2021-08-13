<?php 
namespace NestedPages\Form\Listeners;

class Search extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->setURL();
		$this->redirect();
	}

	/**
	* Set the URL
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']) . '&search=' . sanitize_text_field($_POST['search_term']);
	}
}