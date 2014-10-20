<?php

function nestedpages_syncmenu_handler()
{
	new NP_SyncMenu_Handler;
}

require_once('class-np-handler-base.php');

/**
* Turn on/off menu sync
* @return json response
*/
class NP_SyncMenu_Handler extends NP_BaseHandler {

	public function __construct()
	{
		parent::__construct();
		$this->updateSync();
		$this->sendResponse();
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

}