<?php 
namespace NestedPages\Form\Listeners;

/**
* Turn on/off menu sync
* @return json response
*/
class SyncMenu extends BaseHandler 
{
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
		if ( $this->data['syncmenu'] == 'sync' ) return $this->sync();
		update_option('nestedpages_menusync', 'nosync');
		$this->response = array('status'=>'success', 'message'=> __('Menu sync disabled.'));
	}

	/**
	* Sync the Menu
	*/
	private function sync()
	{
		update_option('nestedpages_menusync', 'sync');
		$this->syncMenu();
		$this->response = array('status'=>'success', 'message'=> __('Menu sync enabled.', 'wp-nested-pages'));
	}
}