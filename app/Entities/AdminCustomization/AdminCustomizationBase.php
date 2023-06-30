<?php 
namespace NestedPages\Entities\AdminCustomization;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;
use NestedPages\Config\SettingsRepository;
use NestedPages\Helpers;

/**
* Admin Customization Base Class (for applying customizations)
*/
abstract class AdminCustomizationBase 
{
	/**
	* Plugin Version
	*/
	protected $plugin_version;

	/**
	* Post Type Repository
	*/
	protected $post_type_repo;

	/**
	* Settings Repository
	*/
	protected $settings;

	/**
	* User Repository
	*/
	protected $user_repo;

	/**
	* Integrations
	*/
	protected $integrations;

	/**
	* The Current User
	*/
	protected $current_user_role;

	/**
	* The plugin directory
	*/
	protected $plugin_dir;
	
	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->user_repo = new UserRepository;
		$this->integrations = new IntegrationFactory;
		$this->settings = new SettingsRepository;
		$this->setPluginVersion();
		$this->plugin_dir = Helpers::plugin_url();
	}

	/**
	* Set the current user's roles
	*/
	protected function setCurrentUserRoles()
	{
		$current_user = wp_get_current_user();
		if ( isset( $current_user->roles[0] ) ) {
			$this->current_user_role = $current_user->roles[0];
		}
	}

	/**
	* Set the Plugin Version
	*/
	protected function setPluginVersion()
	{
		global $np_version;
		$this->plugin_version = $np_version;
	}
}
