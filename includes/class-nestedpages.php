<?php

require_once('class-np-activate.php');
require_once('class-np-sort-handler.php');
require_once('class-np-quickedit-handler.php');
require_once('class-np-dependencies.php');
require_once('class-np-pagelisting.php');
require_once('class-np-newpage.php');
require_once('class-np-navmenu.php');

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
		$this->version = 1.0;
		add_filter( 'plugin_action_links_' . 'nestedpages/nestedpages.php', [ $this, 'settingsLink' ] );
		$this->init();
		$this->formActions();
		add_action('init', array($this, 'addLocalization') );
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
	* Initialize Plugin
	*/
	public function init()
	{
		new NP_Activate;
		new NP_Dependencies;
		new NP_PageListing;
		new NP_NewPage;
		$this->addMenu();
		$this->setVersion();
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_npsort', 'nestedpages_sort_handler' );
			add_action( 'wp_ajax_npquickedit', 'nestedpages_quickedit_handler' );
		}
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	public function settingsLink($links)
	{ 
		$settings_link = '<a href="options-general.php?page=nestedpages">' . __('Settings', 'nestedpages') . '</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}


	/**
	* Localization Domain
	*/
	public function addLocalization()
	{
		load_plugin_textdomain('nestedpages', false, 'nestedpages' . '/languages' );
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


}