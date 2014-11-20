<?php
/**
* Required Version Upgrades
*/
class NP_ActivateUpgrades {

	/**
	* New Version
	*/
	private $new_version;

	/**
	* Current Version
	*/
	private $current_version;


	public function __construct($new_version)
	{
		$this->new_version = $new_version;
		$this->convertMenuToID();
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
	* Convert existing nestedpages_menu option to menu ID rather than string/name
	* @since 1.1.5
	* @todo account for new installs - need to create a new empty nav menu, get the ID, and save it
	*/
	private function convertMenuToID()
	{
		if ( version_compare( $this->current_version, '1.1.5', '<' ) ){
			$menu_option = get_option('nestedpages_menu');
			$menu = get_term_by('name', $menu_option, 'nav_menu');
			update_option('nestedpages_menu', $menu->term_id);
		}
	}


}