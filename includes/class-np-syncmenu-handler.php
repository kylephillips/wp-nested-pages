<?php
/**
* Turn on/off menu sync
*/
function nestedpages_syncmenu_handler()
{
	new NP_SyncMenu_Handler;
}

require_once('class-np-navmenu.php');
class NP_SyncMenu_Handler {

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
	* Response
	* @var array;
	*/
	private $response;


	public function __construct()
	{
		$this->setData();
		$this->validateNonce();
		$this->updateSync();
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
	* Update the sync setting
	*/
	private function updateSync()
	{
		if ( $this->data['syncmenu'] == 'sync' ){
			update_option('nestedpages_menusync', 'sync');
			$this->syncMenu();
			$this->response = array('status'=>'success', 'message'=> __('Menu sync enabled.'));
		} else {
			update_option('nestedpages_menusync', 'nosync');
			$this->response = array('status'=>'success', 'message'=> __('Menu sync disabled.'));
		}
	}


	/**
	* Sync the Nav Menu
	*/
	private function syncMenu()
	{
		$menu = new NP_NavMenu;
		$menu->clearMenu();
		$menu->sync();
	}


	/**
	* Return Response
	*/
	private function sendResponse()
	{
		return wp_send_json($this->response);
	}

}