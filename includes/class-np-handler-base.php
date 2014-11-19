<?php

require_once('class-np-navmenu.php');
require_once('class-np-repository-post.php');

/**
* Base Form Handler Class
*/
abstract class NP_BaseHandler {

	/**
	* Nonce
	* @var string
	*/
	protected $nonce;

	/**
	* Form Data
	* @var array
	*/
	protected $data;

	/**
	* Post Repo
	* @var object
	*/
	protected $post_repo;


	/**
	* Response
	* @var array;
	*/
	protected $response;

	public function __construct()
	{
		$this->post_repo = new NP_PostRepository;
		$this->setData();
		$this->validateNonce();
	}


	/**
	* Set the Form Data
	*/
	protected function setData()
	{
		$this->nonce = sanitize_text_field($_POST['nonce']);
		$data = array();		
		foreach( $_POST as $key => $value ){
			$data[$key] = $value;
		}
		$this->data = $data;
	}


	/**
	* Validate the Nonce
	*/
	protected function validateNonce()
	{
		if ( ! wp_verify_nonce( $this->nonce, 'nestedpages-nonce' ) ){
			$this->response = array( 'status' => 'error', 'message' => __('Incorrect Form Field', 'nestedpages') );
			$this->sendResponse();
			die();
		}
	}


	/**
	* Sync the Nav Menu
	*/
	protected function syncMenu()
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
	* Send a Generic Success Message
	*/
	protected function sendErrorResponse()
	{
		$this->response = array(
			'status' => 'error', 
			'message' => __('There was an error updating the page.', 'nestedpages') 
		);
		$this->sendResponse();
	}


	/**
	* Return Response
	*/
	protected function sendResponse()
	{
		return wp_send_json($this->response);
	}

}