<?php
/**
* Plugin Activation
*/
class NP_Activate {

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
		$this->checkVersion();
	}

	/**
	* Set Default Options
	*/
	public function setOptions()
	{
		if ( !get_option('nestedpages_menusync') ){
			update_option('nestedpages_menusync', 'sync');
		}
	}


	/**
	* Check Version
	*/
	public function checkVersion()
	{
		$version = ( floatval(get_bloginfo('version')) );
		if ( $version < 3.9 ){
			add_action( 'admin_notices', array($this, 'versionNotice' ) );
			return false;
		} else {
			return true;
		}
	}

	/**
	* Version Notice
	*/
	public function versionNotice()
	{
		echo '<div class="updated error"><p>Nested Pages requires Wordpress version 3.9 or higher</p></div>';
	}


}