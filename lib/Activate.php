<?php namespace NestedPages;
/**
* Plugin Activation
*/
class Activate {

	public function __construct()
	{
		register_activation_hook( dirname( dirname(__FILE__) ) . '/nestedpages.php', [ $this, 'install' ] );
	}

	/**
	* Activation Hook
	*/
	public function install()
	{
		$this->setOptions();
	}

	/**
	* Set Default Options
	*/
	public function setOptions()
	{
		//if ( !get_option('wpduel_post_type') ) update_option('wpduel_post_type', 'contender');
	}


}