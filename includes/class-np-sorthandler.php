<?php

function nestedpages_sort_handler()
{
	new NP_SortHandler;
}

/**
* Handles processing sortable pages
* updates menu order & page parents
* @return json response
*/
require_once('class-np-postrepository.php');

class NP_SortHandler {

	/**
	* Form Data
	* @var array
	*/
	private $data;


	/**
	* Post Factory
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
		$this->process();	
	}


	/**
	* Process the Form
	*/
	private function process()
	{
		$this->setData();
		$this->validateData();
		$this->updateOrder();
		$this->sendResponse();
	}


	/**
	* Set the Form Data
	*/
	private function setData()
	{
		$nonce = sanitize_text_field($_POST['nonce']);
		
		$this->data = array(
			'nonce' => $nonce,
			'list' => $_POST['list']
		);
	}


	/**
	* Validate the form data
	*/
	private function validateData()
	{
		$data = $this->data;

		// Validate Nonce
		if ( ! wp_verify_nonce( $data['nonce'], 'nestedpages-nonce' ) ){
			$this->response = array( 'status' => 'error', 'message' => 'Incorrect Form Field' );
			$this->sendResponse();
			die();
		}
	}


	/**
	* Update Post Order
	*/
	private function updateOrder()
	{
		$posts = $this->data['list'];
		$order = $this->post_repo->updateOrder($posts);
		if ( $order ){
			$this->response = array('status' => 'success', 'message' => 'Page order successfully updated.');
		} else {
			$this->response = array('status'=>'error', 'message'=>'There was an order updating the page order.');
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