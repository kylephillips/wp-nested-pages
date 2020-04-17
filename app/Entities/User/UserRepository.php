<?php
namespace NestedPages\Entities\User;

use NestedPages\Config\SettingsRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* User Repository
* @since 1.1.7
*/
class UserRepository
{
	/**
	* Settings Repository
	* @var object
	*/
	private $settings;

	/**
	* Plugin Integrations
	* @var object
	*/
	private $integrations;

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->integrations = new IntegrationFactory;
	}

	/**
	* Return Current User's Roles
	* @return array
	* @since 1.1.7
	*/
	public function getRoles()
	{
		global $current_user;

		// If current user is superadmin (WP Multisite) add administrator to the roles array
		if (function_exists('is_multisite') && is_multisite() && is_super_admin()) {
			$current_user->roles[] = 'administrator';
		}

		return $current_user->roles;
	}

	/**
	* Get all roles that arent admin, contributor or subscriber
	* @return array
	* @since 1.1.7
	*/
	public function allRoles($exclude = array('Administrator', 'Contributor', 'Subscriber', 'Author') )
	{
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
		$roles = [];
		if ( !is_array($exclude) ) $exclude = [];
		foreach($editable_roles as $key=>$editable_role){
			if ( !in_array($editable_role['name'], $exclude) ){
				$role = [
					'name' => $key,
					'label' => $editable_role['name']
				];
				array_push($roles, $role);
			}
		}
		return $roles;
	}

	/**
	* Get a single role
	* @since 3.0
	*/
	public function getSingleRole($role = 'administrator')
	{
		global $wp_roles;
		if ( isset($wp_roles->roles[$role]) ) return $wp_roles->roles[$role];
		return false;
	}

	/**
	* Get the capabilities for a role
	*/
	public function getSingleRoleCapabilities($role = 'administrator')
	{
		global $wp_roles;
		if ( isset($wp_roles->roles[$role]) ) :
			$capabilities = $wp_roles->roles[$role]['capabilities'];
			if ( $role == 'administrator' && class_exists('GFCommon') ) $capabilities['gform_full_access'] = true;
			return apply_filters('nestedpages_capabilities', $capabilities, $role);
		endif;
		return false;
	}

	/**
	* Can current user sort pages
	* @return boolean
	* @since 1.1.7
	* @see NestedPages\Entities\User\UserCapabilities
	*/
	public function canSortPosts($post_type = 'page')
	{
		$roles = $this->getRoles();
		$user_can_sort = false;
		$roles_cansort = get_option('nestedpages_allowsorting', []);
		if ( $roles_cansort == "" ) $roles_cansort = [];

		foreach($roles as $role){
			if ( $role == 'administrator' ) return true;
			if ( in_array($role, $roles_cansort) ) $user_can_sort = true; // Plugin Option
			$role_obj = get_role($role);
			if ( $role_obj->has_cap("nestedpages_sorting_$post_type") ) $user_can_sort = true; // Custom Capability
		}

		return $user_can_sort;
	}

	/**
	* Can current user view the Nested Pages Sort View
	* @return boolean
	* @since 3.1.9
	*/
	public function canViewSorting($post_type = 'page')
	{
		$roles = $this->getRoles();
		$viewable_roles = $this->settings->sortViewEnabled();
		$user_can_view = false;

		foreach($roles as $role){
			if ( $role == 'administrator' ) return true;
			if ( in_array($role, $viewable_roles) ) $user_can_view = true; // Custom Capability
		}
		
		return $user_can_view;
	}

	/**
	* Can the user publish to post type
	*/
	public function canPublish($post_type = 'post')
	{
		if ( $post_type == 'page' ) {
			return ( current_user_can('publish_pages') ) ? true : false;
		}
		if ( current_user_can('publish_posts') ) return true;
		return false;
	}

	/**
	* Can the user add new posts for review?
	*/
	public function canSubmitPending($post_type = 'post')
	{
		if ( $post_type == 'page' ) {
			return ( current_user_can('edit_pages') ) ? true : false;
		}
		if ( current_user_can('edit_posts') ) return true;
		return false;
	}

	/**
	* Get an array of all users/ids
	* @since 1.3.0
	* @return array
	*/
	public function allUsers()
	{
		$users = get_users([
			'fields' => ['ID', 'display_name']
		]);
		return $users;
	}

	/**
	* Get User's Visible Pages
	* @since 1.3.4
	* @return array - array of pages user has toggled visible
	*/
	public function getVisiblePages()
	{
		return unserialize(get_user_meta(get_current_user_id(), 'np_visible_posts', true));
	}

	/**
	* Update User's Visible Pages
	*/
	public function updateVisiblePages($post_type, $ids)
	{
		$visible = $this->getVisiblePages();
		if ( $this->integrations->plugins->wpml->installed ) $ids = $this->integrations->plugins->wpml->getAllTranslatedIds($ids);
		$visible[$post_type] = $ids;
		update_user_meta(
			get_current_user_id(),
			'np_visible_posts',
			serialize($visible)
		);
	}
}