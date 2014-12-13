<?php
/**
* Plugin Activation
*/
require_once('class-np-activate-upgrades.php');
class NP_Activate {

	/**
	* Plugin Version
	*/
	private $version;


	public function __construct()
	{
		$this->install();
	}


	/**
	* Activation Hook
	*/
	public function install()
	{
		$this->version = '1.1.8';
		new NP_ActivateUpgrades($this->version);
		$this->setVersion();
		$this->setOptions();
	}


	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		update_option('nestedpages_version', $this->version);
	}
	


	/**
	* Set Default Options
	*/
	private function setOptions()
	{
		if ( !get_option('nestedpages_menusync') ){
			update_option('nestedpages_menusync', 'sync');
		}
	}


}