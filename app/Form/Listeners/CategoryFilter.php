<?php 
namespace NestedPages\Form\Listeners;

/**
* Filter Pages by Category
*/
class CategoryFilter 
{
	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	public function __construct()
	{
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


	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}