<?php
require_once('class-np-helpers.php');
/**
* Plugin Settings
*/
class NP_Settings {


	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'registerSettingsPage' ) );
	}


	/**
	* Register the settings page
	*/
	public function registerSettingsPage()
	{
		add_options_page( 
			'Nested Pages Settings',
			'Nested Pages',
			'manage_options',
			'nested-pages-settings', 
			array( $this, 'settingsPage' ) 
		);
	}


	/**
	* Display the Settings Page
	*/
	public function settingsPage()
	{
		include( NP_Helpers::view('settings') );
	}	

}