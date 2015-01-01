<?php namespace NestedPages\Activation\Updates;
/**
* Required Version Upgrades
*/
class Updates {

	/**
	* New Version
	*/
	private $new_version;

	/**
	* Current Version
	*/
	private $current_version;

	/**
	* Run the Updates
	* @var string
	*/
	public function run($new_version)
	{
		$this->new_version = $new_version;
		$this->setCurrentVersion();
		$this->addMenu();
		$this->convertMenuToID();
		$this->enablePagePostType();
		$this->enabledDatepicker();
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
			$menu_id = wp_create_nav_menu('Nested Pages');
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
	* @since 1.2.1
	*/
	private function enablePagePostType()
	{
		if ( version_compare( $this->current_version, '1.3.0', '<' ) ){
			$enabled = get_option('nestedpages_posttypes');
			$default = array('page' => array(
				'replace_menu' => true
			));
			if ( !$enabled ) update_option('nestedpages_posttypes', $default);
		}
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



}