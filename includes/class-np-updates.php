<?php
/**
* Updates
*/
class NP_Updates {

	/**
	* Plugin Version
	*/
	private $version;

	public function __construct()
	{
		$this->setVersion();
		$this->addMenuOption();
	}

	/**
	* Set User's Plugin version
	*/
	private function setVersion()
	{
		$this->version = get_option('nestedpages_version');
	}

	/**
	* Make sure menu option is
	*/
	private function addMenuOption()
	{
		if ( !get_option('nestedpages_menu') )
			update_option('nestedpages_menu', 'nestedpages');
	}

}