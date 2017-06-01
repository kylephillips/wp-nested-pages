<?php 
namespace NestedPages;

/**
* Primary Plugin Bootstrap
*/
class Bootstrap 
{
	public function __construct()
	{
		$this->initializePlugin();
		add_action( 'wp_loaded', array($this, 'wpLoaded'));
		add_action( 'init', array($this, 'initializeWordPress') );
		add_filter( 'plugin_action_links_' . 'wp-nested-pages/nestedpages.php', array($this, 'settingsLink' ) );
	}

	/**
	* WP Loaded
	*/
	public function wpLoaded()
	{
		new Activation\Activate;
		new Redirects;
	}

	/**
	* Initialize Plugin
	*/
	private function initializePlugin()
	{
		new Entities\PostType\RegisterPostTypes;
		new Entities\Post\PostTrashActions;
		new Entities\Listing\ListingActions;
		new Entities\NavMenu\NavMenuActions;
		new Entities\NavMenu\NavMenuTrashActions;
		new Form\Events;
		new Config\Settings;
	}

	/**
	* Wordpress Initialization Actions
	*/
	public function initializeWordPress()
	{
		new Entities\AdminMenu\AdminMenu;
		new Entities\DefaultList\DefaultListFactory;
		$this->addLocalization();
	}

	/**
	* Localization Domain
	*/
	public function addLocalization()
	{
		load_plugin_textdomain(
			'wp-nested-pages', 
			false, 
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
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