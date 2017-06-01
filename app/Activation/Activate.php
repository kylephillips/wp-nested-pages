<?php 
namespace NestedPages\Activation;

use NestedPages\Activation\Updates\Updates;

/**
* Plugin Activation
*/
class Activate 
{
	/**
	* Plugin Version
	*/
	private $version;

	/**
	* Updates
	* @var object
	*/
	private $updates;

	public function __construct()
	{
		$this->setVersion();
		$this->updates = new Updates;
		$this->install();
	}

	/**
	* Activation Hook
	*/
	public function install()
	{
		$this->updates->run($this->version);
		$this->saveVersion();
		$this->setOptions();
		new Dependencies;
	}

	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		global $np_version;
		$this->version = $np_version;
	}

	/**
	* Set the Plugin Version
	*/
	private function saveVersion()
	{
		update_option('nestedpages_version', $this->version);
	}
	
	/**
	* Set Default Options
	*/
	private function setOptions()
	{
		if ( !get_option('nestedpages_menusync') ){
			update_option('nestedpages_menusync', 'nosync');
		}
	}
}