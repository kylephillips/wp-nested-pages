<?php 
namespace NestedPages\Form\Listeners;

/**
* Filter Pages by Category
*/
class CategoryFilter extends BaseHandler
{
	public function __construct()
	{
		parent::__construct();
		$this->setURL();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
		$this->setCategories();
	}

	/**
	* Set Order parameters
	*/
	private function setCategories()
	{
		$this->url .= '&category=' . sanitize_text_field($_POST['np_category']);
	}
}