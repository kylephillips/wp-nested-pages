<?php
/**
* User Repository
* @since 1.1.7
*/
class NP_UserRepository {


	/**
	* Return Current User's Roles
	* @return array
	* @since 1.1.7
	*/
	public function getRoles()
	{
		global $current_user;
		return $current_user->roles;
	}


	/**
	* Get all roles that arent admin, contributor or subscriber
	* @return array
	* @since 1.1.7
	*/
	public function allRoles()
	{
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
		$roles = array();
		$exclude = array('Administrator', 'Contributor', 'Subscriber', 'Author');
		foreach($editable_roles as $editable_role){
			if ( !in_array($editable_role['name'], $exclude) ){
				array_push($roles, $editable_role['name']);
			}
		}
		return $roles;
	}


	/**
	* Can current user sort pages
	* @return boolean
	* @since 1.1.7
	*/
	public function canSortPages()
	{
		$roles = $this->getRoles();
		$cansort = get_option('nestedpages_allowsorting', array());
		if ( $cansort == "" ) $cansort = array();
		
		if ( current_user_can('edit_theme_options') ) return true;
		foreach($roles as $role){
			if ( in_array(ucfirst($role), $cansort) ) return true;
		}
		return false;
	}

}