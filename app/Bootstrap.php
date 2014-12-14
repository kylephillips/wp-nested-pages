<?php namespace NestedPages;

/**
* Primary Plugin Bootstrap
*/
class Bootstrap {


	public function __construct()
	{
		$this->init();
		$this->formActions();
		add_action( 'init', array($this, 'listPages') );
		add_action( 'init', array($this, 'addLocalization') );
		add_filter( 'plugin_action_links_' . 'wp-nested-pages/nestedpages.php', array($this, 'settingsLink' ) );
	}

	/**
	* Initialize Plugin
	*/
	public function init()
	{
		new Activation\Activate;
		new Activation\Dependencies;
		new Entities\Redirects;
		new Entities\PostTypes;
		new Settings\Settings;
	}

	/**
	* Page Listing
	* @since 1.1.6 - Moved into init due to Multisite bug
	*/
	public function listPages()
	{
		new Controllers\AdminMenuController;
		new Controllers\AdminSubmenuController;
		new Controllers\PageListingController;
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		new Handlers\Actions;
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


}