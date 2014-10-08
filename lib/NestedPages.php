<?php namespace NestedPages;

require_once('Activate.php');
require_once('SortHandler.php');
require_once('Dependencies.php');
require_once('PageListing.php');

/**
* Primary Plugin Class
*/
class NestedPages {

	/**
	* Plugin Version
	*/
	private $version;


	public function __construct()
	{
		$this->version = 0.1;
		add_filter( 'plugin_action_links_' . 'nestedpages/nestedpages.php', [ $this, 'settingsLink' ] );
		$this->init();
		$this->formActions();
	}

	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		if ( !get_option('wppages_version') ){
			update_option('wppages_version', $this->version);
		}
		elseif ( get_option('wppages_version') < $this->version ){
			update_option('wppages_version', $this->version);	
		}
	}

	/**
	* Initialize Plugin
	*/
	public function init()
	{
		new Activate;
		new Dependencies;
		new PageListing;
		$this->setVersion();
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_npsort', 'nestedpages_sort_handler' );
		}
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	public function settingsLink($links)
	{ 
		$settings_link = '<a href="options-general.php?page=nestedpages">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}


}