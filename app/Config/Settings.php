<?php 

namespace NestedPages\Config;

use NestedPages\Helpers;
use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Config\SettingsRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Plugin Settings
*/
class Settings 
{

	/**
	* Nested Pages Menu
	* @var object
	*/
	private $menu;

	/**
	* User Repository
	*/
	private $user_repo;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Post Types
	*/
	private $post_types;

	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* Plugin Integration
	*/
	private $integrations;

	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'registerSettingsPage' ) );
		add_action( 'admin_init', array( $this, 'registerSettings' ) );
		add_action( 'updated_option', array( $this, 'updateMenuName'), 10, 3);
		$this->user_repo = new UserRepository;
		$this->settings = new SettingsRepository;
		$this->post_type_repo = new PostTypeRepository;
		$this->integrations = new IntegrationFactory;
	}

	/**
	* Register the settings page
	* @see admin_menu
	*/
	public function registerSettingsPage()
	{
		add_options_page( 
			__('Nested Pages Settings', 'nestedpages'),
			__('Nested Pages', 'nestedpages'),
			'manage_options',
			'nested-pages-settings', 
			array( $this, 'settingsPage' ) 
		);
	}

	/**
	* Register the settings
	* @see admin_init
	*/
	public function registerSettings()
	{
		register_setting( 'nestedpages-general', 'nestedpages_menu' );
		register_setting( 'nestedpages-general', 'nestedpages_menusync' );
		register_setting( 'nestedpages-general', 'nestedpages_disable_menu' );
		register_setting( 'nestedpages-general', 'nestedpages_ui' );
		register_setting( 'nestedpages-general', 'nestedpages_allowsorting' );
		register_setting( 'nestedpages-posttypes', 'nestedpages_posttypes' );
	}

	/**
	* Update the menu name if option is updated
	* @see updated_option in wp-includes/option.php
	* @since 1.1.5
	*/
	public function updateMenuName($option, $old_value, $value)
	{
		if ( $option == 'nestedpages_menu' ){

			$menu = get_term_by('id', $old_value, 'nav_menu');
			if ( $menu ) {
				delete_option('nestedpages_menu'); // Delete the option to prevent infinite loop
				update_option('nestedpages_menu', $old_value);
				wp_update_term($menu->term_id, 'nav_menu', array(
					'name' => $value,
					'slug' => sanitize_title($value)
				));
			}
		}
	}

	/**
	* Set the Menu Object
	* @since 1.1.5
	*/
	private function setMenu()
	{
		$menu_id = get_option('nestedpages_menu');
		$this->menu = get_term_by('id', $menu_id, 'nav_menu');
	}

	/**
	* Get Post Types
	* @since 1.2.0
	*/
	private function getPostTypes()
	{
		return $this->post_type_repo->getPostTypesObject();
	}

	/**
	* Display the Settings Page
	* Callback for registerSettingsPage method
	*/
	public function settingsPage()
	{
		$this->setMenu();
		$tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		include( Helpers::view('settings/settings') );
	}	

}