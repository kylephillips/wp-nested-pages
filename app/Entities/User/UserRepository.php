<?php 

namespace NestedPages\Entities\User;

/**
* User Repository
* @since 1.1.7
*/
class UserRepository 
{

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
		foreach($editable_roles as $key=>$editable_role){
			if ( !in_array($editable_role['name'], $exclude) ){
				$role = array(
					'name' => $key,
					'label' => $editable_role['name']
				);
				array_push($roles, $role);
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
		
		foreach($roles as $role){
			if ( $role == 'administrator' ) return true;
			if ( in_array($role, $cansort) ) return true;
		}
		return false;
	}

	/**
	* Get an array of all users/ids
	* @since 1.3.0
	* @return array
	*/ 
	public function allUsers()
	{
		$users = get_users(array(
			'fields' => array('ID', 'display_name')
		));
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
		$visible[$post_type] = $ids;
		update_user_meta(
			get_current_user_id(),
			'np_visible_posts',
			serialize($visible)
		);
	}

}