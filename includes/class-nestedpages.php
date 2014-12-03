<?php
// Activate and Check Versions
require_once('class-np-activate.php');

// Form Handlers
require_once('class-np-handler-sort.php');
require_once('class-np-handler-quickedit.php');
require_once('class-np-handler-quickedit-redirect.php');
require_once('class-np-handler-newredirect.php');
require_once('class-np-handler-syncmenu.php');
require_once('class-np-handler-nesttoggle.php');
require_once('class-np-handler-gettax.php');

// Required Classes
require_once('class-np-dependencies.php');
require_once('class-np-pagelisting.php');
require_once('class-np-newpage.php');
require_once('class-np-redirects.php');
require_once('class-np-posttypes.php');
require_once('class-np-settings.php');

/**
* Primary Plugin Class
*/
class NestedPages {


	public function __construct()
	{
		$this->init();
		$this->formActions();
		add_action( 'init', array($this, 'listPages') );
		add_action( 'init', array($this, 'addLocalization') );
		add_action( 'admin_init', array($this, 'verifyPostType') );
		add_filter( 'plugin_action_links_' . 'wp-nested-pages/nestedpages.php', array($this, 'settingsLink' ) );
	}

	/**
	* Initialize Plugin
	*/
	public function init()
	{
		new NP_Activate;
		new NP_Dependencies;
		new NP_NewPage;
		new NP_Redirects;
		new NP_PostTypes;
		new NP_Settings;
	}

	/**
	* Page Listing
	* @since 1.1.6 - Moved into init due to Multisite bug
	*/
	public function listPages()
	{
		new NP_PageListing;
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
			add_action( 'wp_ajax_npnesttoggle', 'nestedpages_nesttoggle_handler' );
			add_action( 'wp_ajax_npquickeditredirect', 'nestedpages_quickedit_redirect_handler' );
			add_action( 'wp_ajax_npnewredirect', 'nestedpages_new_redirect');
			add_action( 'wp_ajax_gettax', 'nestedpages_get_tax' );
		}
	}


	/**
	* Localization Domain
	*/
	public function addLocalization()
	{
		load_plugin_textdomain('nestedpages', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	public function settingsLink($links)
	{ 
  		$settings_link = '<a href="options-general.php?page=nested-pages-settings">' . __('Settings') . '</a>'; 
  		array_unshift($links, $settings_link); 
  		return $links; 
	}


	/**
	* Check for the page post type before doing anything
	*/
	public function verifyPostType()
	{
		if ( !get_post_type_object( 'page' ) ){
			$plugin = dirname(dirname( __FILE__ ) ) . '/nestedpages.php';
			deactivate_plugins( $plugin );
    		wp_die('<p>Nested Pages has been deactivated. This plugin requires the <strong>"page"</strong> post type to be enabled and available for use. Please disable any plugins that may be interfering with this post type and reactivate.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
		}
	}


}