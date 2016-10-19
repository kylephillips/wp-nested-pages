<?php 

namespace NestedPages\Entities\PluginIntegration;

/**
* Editorial Access Manager Integration
* @link https://wordpress.org/plugins/editorial-access-manager/
*/

class EditorialAccessManager 
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed;

	/**
	* Current User ID
	*/
	private $user;


	public function __construct()
	{
		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	* Initialize Plugin Integration
	*/
	public function init() {
		if ( class_exists('Editorial_Access_Manager') ){
			$this->installed = true;
			$this->user = wp_get_current_user();
		} 
	}

	/**
	* Does the current user have access to the specified post id?
	* @return boolean
	*/
	public function hasAccess($post_id)
	{
		if ( $this->abortCheck() ) return true;

		$access_meta = get_post_meta($post_id, 'eam_enable_custom_access', true);
		
		if ( $access_meta == 'users' ){
			$allowed_users = (array) get_post_meta($post_id, 'eam_allowed_users', true);
			if ( isset($allowed_users[0]) && $allowed_users[0] == "" ) return true;
			if ( !in_array($this->user->ID, $allowed_users) ) return false;
		}

		if ( $access_meta == 'roles' ){
			$allowed_roles = (array) get_post_meta($post_id, 'eam_allowed_roles', true);
			if ( count( array_diff( $this->user->roles, $allowed_roles ) ) >= 1 ) return false;
		}

		return true;
	}

	/**
	* Abort Role Check?
	* @return boolean
	*/
	private function abortCheck()
	{
		if ( !$this->installed ) return true;
		if ( in_array('administrator', $this->user->roles) ) return true;
		return false;
	}

}