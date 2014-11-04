<?php
/**
* Plugin Activation
*/
require_once('class-np-navmenu.php');
class NP_Activate {

	/**
	* Plugin Version
	*/
	private $version;


	public function __construct()
	{
		register_activation_hook( dirname( dirname(__FILE__) ) . '/nestedpages.php', array($this, 'install') );
		$this->version = 1.1;
		$this->setVersion();
		$this->addMenu();
	}


	/**
	* Activation Hook
	*/
	public function install()
	{
		$this->checkVersions();
		$this->setOptions();
	}


	/**
	* Check Wordpress and PHP versions
	*/
	private function checkVersions( $wp = '3.9', $php = '5.3.0' ) {
		global $wp_version;
		if ( version_compare( PHP_VERSION, $php, '<' ) )
			$flag = 'PHP';
		elseif ( version_compare( $wp_version, $wp, '<' ) )
			$flag = 'WordPress';
		else 
			return;
		$version = 'PHP' == $flag ? $php : $wp;
		deactivate_plugins( basename( __FILE__ ) );
		
		wp_die('<p><strong>Nested Pages</strong> plugin requires'.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	}


	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		if ( !get_option('nestedpages_version') ){
			update_option('nestedpages_version', $this->version);
		}
		elseif ( get_option('nestedpages_version') < $this->version ){
			update_option('nestedpages_version', $this->version);	
		}
	}


	/**
	* Add the nav menu
	*/
	public function addMenu()
	{
		$menu_e = get_term_by('slug', 'nestedpages', 'nav_menu');
		if ( !$menu_e ){
			$menu = new NP_NavMenu;
			$menu->addMenu();
		}
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