<?php
// Activate and Check Versions
require_once('class-np-activate.php');

// Form Handlers
require_once('class-np-handler-sort.php');
require_once('class-np-handler-quickedit.php');
require_once('class-np-handler-syncmenu.php');

// Required Classes
require_once('class-np-dependencies.php');
require_once('class-np-pagelisting.php');
require_once('class-np-newpage.php');

/**
* Primary Plugin Class
*/
class NestedPages {


	public function __construct()
	{
		$this->init();
		$this->formActions();
		add_action('init', array($this, 'addLocalization') );
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
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_npsort', 'nestedpages_sort_handler' );
			add_action( 'wp_ajax_npquickedit', 'nestedpages_quickedit_handler' );
			add_action( 'wp_ajax_npsyncmenu', 'nestedpages_syncmenu_handler' );
		}
	}


	/**
	* Localization Domain
	*/
	public function addLocalization()
	{
		load_plugin_textdomain('nestedpages', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}


}