<?php

function nestedpages_quickedit_handler()
{
	new NP_QuickEdit_Handler;
}


/**
* Handles processing the quick edit form
* @return json response
*/
require_once('class-np-postrepository.php');

class NP_QuickEdit_Handler {

	/**
	* Nonce
	* @var string
	*/
	private $nonce;

	/**
	* Form Data
	* @var array
	*/
	private $data;


	/**
	* Post Repo
	* @var object
	*/
	private $post_repo;


	/**
	* Response
	* @var array;
	*/
	private $response;



	public function __construct()
	{
		$this->post_repo = new NP_PostRepository;
		$this->setData();
		$this->validateNonce();
		$this->updatePost();
		$this->syncMenu();
		$this->sendResponse();
	}


	/**
	* Set the Form Data
	*/
	private function setData()
	{
		$this->nonce = sanitize_text_field($_POST['nonce']);
		$data = array();		
		foreach( $_POST as $key => $value ){
			$data[$key] = $value;
		}
		if ( !isset($_POST['comment_status']) ) $data['comment_status'] = 'closed';
		$this->data = $data;
	}


	/**
	* Validate the Nonce
	*/
	private function validateNonce()
	{
		if ( ! wp_verify_nonce( $this->nonce, 'nestedpages-nonce' ) ){
			$this->response = array( 'status' => 'error', 'message' => __('Incorrect Form Field') );
			$this->sendResponse();
			die();
		}
	}


	/**
	* Update the Post
	*/
	private function updatePost()
	{
		$update = $this->post_repo->updatePost($this->data);
		if ( $update ){
			
			$data = $this->data;
			$data['nav_status'] = ( isset($data['nav_status']) ) ? 'hide' : 'show';

			$this->response = array(
				'status' => 'success', 
				'message' => __('Post successfully updated'), 
				'post_data' => $data
			);
		} else {
			$this->response = array(
				'status' => 'error', 
				'message' => __('There was an error updating the page.') 
			);
		}
	}


	/**
	* Sync the Nav Menu
	*/
	private function syncMenu()
	{
		if ( $_POST['syncmenu'] == 'sync' ){
			$menu = new NP_NavMenu;
			$menu->clearMenu();
			$menu->sync();
			update_option('nestedpages_menusync', 'sync');
		} else {
			update_option('nestedpages_menusync', 'nosync');
		}
	}


	/**
	* Return Response
	*/
	private function sendResponse()
	{
		return wp_send_json($this->response);
	}
}