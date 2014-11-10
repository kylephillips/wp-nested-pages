<?php
require_once('class-np-helpers.php');
/**
* Plugin Settings
*/
class NP_Settings {


	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'registerSettingsPage' ) );
		add_action( 'admin_init', array($this, 'registerSettings' ) );
		add_action('updated_option', array($this, 'updateMenuName'), 10, 3);
	}


	/**
	* Register the settings page
	*/
	public function registerSettingsPage()
	{
		add_options_page( 
			'Nested Pages Settings',
			'Nested Pages',
			'manage_options',
			'nested-pages-settings', 
			array( $this, 'settingsPage' ) 
		);
	}


	/**
	* Register the settings
	*/
	public function registerSettings()
	{
		register_setting( 'nestedpages-general', 'nestedpages_menu' );
		register_setting( 'nestedpages-general', 'nestedpages_menusync' );
	}


	/**
	* Update the menu name if custom is provided
	* @since 1.1.11
	*/
	public function updateMenuName($option, $old_value, $value)
	{
		if ( $option == 'nestedpages_menu' ){
			$menu = get_term_by('name', $old_value, 'nav_menu');
			wp_update_term($menu->term_id, 'nav_menu', array(
				'name' => $value
			));
		}
	}


	/**
	* Display the Settings Page
	*/
	public function settingsPage()
	{
		$menu_name = ( get_option('nestedpages_menu') ) ? get_option('nestedpages_menu') : 'nestedpages';
		include( NP_Helpers::view('settings') );
	}	

}