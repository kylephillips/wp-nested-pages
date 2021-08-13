<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\Post\PostUpdateRepository;

/**
* Perform a Bulk Edit
*/
class BulkEdit extends BaseHandler
{
	/**
	* Post IDs (Comma-separated)
	* @var string
	*/
	private $post_ids;

	public function __construct()
	{
		parent::__construct();
		$this->setURL();
		$this->setFieldData();
		$this->performEdits();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
	}

	/**
	* Set the field data (Removed unchanged fields so they're not overwritten)
	*/
	private function setFieldData()
	{
		foreach ( $_POST as $field => $value ){
			if ( $value == '' || $value == '-1' ) unset($_POST[$field]);
		}
		$this->data = $_POST;
	}

	/**
	* Perform the Bulk Edits
	*/
	private function performEdits()
	{
		foreach ( $this->data['post_ids'] as $post_id ){
			$data = $this->data;
			$data['post_id'] = $post_id;
			$this->post_update_repo->updatePost($data, $append_taxonomies = true);
		}
	}
}