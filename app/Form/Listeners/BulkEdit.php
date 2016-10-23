<?php 

namespace NestedPages\Form\Listeners;

/**
* Perform a Bulk Edit
*/
class BulkEdit 
{

	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	/**
	* Post IDs (Comma-separated)
	* @var string
	*/
	private $post_ids;

	public function __construct()
	{
		$this->setURL();
		$this->setPostIds();
		$this->performEdits();
		// $this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
	}

	/**
	* Set the Post IDs
	*/
	private function setPostIds()
	{
		
	}

	/**
	* Perform the Bulk Edits
	*/
	private function performEdits()
	{
		foreach ( $_POST as $field => $value ){
			// Unchanged Values
			if ( $value == '' || $value == '-1' ) unset($_POST[$field]);
		}
		var_dump($_POST);
		die();
	}

	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}