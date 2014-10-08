<?php

function nestedpages_sort_handler()
{
	new SortHandler;
}

/**
* Handles processing sortable pages
* updates menu order & page parents
* @return json response
*/
class SortHandler {

	/**
	* Form Data
	* @var array
	*/
	private $data;


	public function __construct()
	{
		$this->setData();
		$this->validateData();
		$this->response();
	}


	/**
	* Set the Form Data
	*/
	public function setData()
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
			$output = json_encode(array(
				'status' => 'error',
				'message' => 'Busted Yo!'
			));
			echo $output;
			die();
		}
	}


	/**
	* Return Response
	*/
	private function response()
	{
		return wp_send_json(array('data'=>$this->data));
	}



}