<?php 
namespace NestedPages\Activation\Updates;

use NestedPages\Entities\NavMenu\NavMenuRepository;
use NestedPages\Entities\NavMenu\NavMenuSyncListing;
use NestedPages\Activation\Updates\CustomFieldsToHidden;

/**
* Required Version Upgrades
*/
class Updates 
{
	/**
	* New Version
	*/
	private $new_version;

	/**
	* Current Version
	*/
	private $current_version;

	/**
	* Nav Menu Repository
	*/
	private $nav_menu_repo;

	/**
	* Run the Updates
	* @var string
	*/
	public function run($new_version)
	{
		$this->new_version = $new_version;
		$this->nav_menu_repo = new NavMenuRepository;
		$this->setCurrentVersion();
		$this->clearMenu();
		$this->addMenu();
		$this->convertMenuToID();
		$this->enablePagePostType();
		$this->enabledDatepicker();
		$this->convertCustomFieldsToHidden();
	}

	/**
	* Set the plugin version
	*/
	private function setCurrentVersion()
	{
		$this->current_version = ( get_option('nestedpages_version') )
			? get_option('nestedpages_version') : $this->new_version;
	}

	/**
	* Add an empty Nested Pages menu if there isn't one
	* @since 1.1.5
	*/
	private function addMenu()
	{
		if ( !get_option('nestedpages_menu') ){
			$menu_id = $this->nav_menu_repo->getMenuIDFromTitle('Nested Pages');
			if ( !$menu_id ) $menu_id = wp_create_nav_menu('Nested Pages');
			update_option('nestedpages_menu', $menu_id);
		}
	}
	
	/**
	* Convert existing nestedpages_menu option to menu ID rather than string/name
	* @since 1.1.5
	*/
	private function convertMenuToID()
	{
		if ( version_compare( $this->current_version, '1.1.5', '<' ) ){
			$menu_option = get_option('nestedpages_menu');
			$menu = get_term_by('name', $menu_option, 'nav_menu');
			if ( $menu ){
				delete_option('nestedpages_menu');
				update_option('nestedpages_menu', $menu->term_id);
			} else {
				delete_option('nestedpages_menu');
				$menu_id = wp_create_nav_menu('Nested Pages');
				update_option('nestedpages_menu', $menu_id);
			}
		}
	}

	/**
	* Make Page Post Type Enabled by Default
	* Option can be blank, using get_option returns false if blank
	* @since 1.3.5
	*/
	private function enablePagePostType()
	{	
		global $wpdb;
		$options_table = $wpdb->prefix . 'options';
		$sql = "SELECT * FROM $options_table WHERE option_name = 'nestedpages_posttypes'";
		$results = $wpdb->get_results($sql);
		if ( $results ) return;
		update_option('nestedpages_posttypes', array(
			'page' => array(
				'replace_menu' => true
			)
		));
	}

	/**
	* Enable the Datepicker
	*/
	private function enabledDatepicker()
	{
		if ( version_compare( $this->current_version, '1.3.1', '<' ) ){
			$enabled = get_option('nestedpages_ui', false);
			$default = array(
				'datepicker' => 'true'
			);
			if ( !$enabled ) update_option('nestedpages_ui', $default);
		}
	}

	/**
	* Regenerate the synced menu
	*/
	private function clearMenu()
	{
		if ( version_compare( $this->current_version, '1.5.2', '<' ) ){
			$menu_id = $this->nav_menu_repo->getMenuID();
			if ( $menu_id ) $this->nav_menu_repo->clearMenu($menu_id);
			if ( get_option('nestedpages_menusync') !== 'sync' ) return;
			$syncer = new NavMenuSyncListing;
			$syncer->sync();
		}
	}

	/**
	* Convert the Nested Pages custom fields to hidden fields
	*/
	private function convertCustomFieldsToHidden()
	{
		if ( version_compare( $this->current_version, '1.7.0', '<=' ) ){
			new CustomFieldsToHidden;
		}
	}
}