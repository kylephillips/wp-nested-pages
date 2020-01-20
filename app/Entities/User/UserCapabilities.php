<?php
namespace NestedPages\Entities\User;

/**
* Register custom user roles
*/
class UserCapabilities
{
	public function __construct()
	{		
		add_action('plugins_loaded', [$this, 'addSortingCapabilities']);
	}

	/**
	* Adds custom capability of nestedpages_sort_$type
	* $type is the post type
	* 
	*/
	public function addSortingCapabilities()
	{
		$post_types = get_post_types(['public' => true]);
		$invalid = ['attachment'];
		$granted_roles = ['administrator'];
		$roles = wp_roles();
		foreach ( $post_types as $type ) :
			if ( in_array($type, $invalid) ) continue;
			foreach ( $roles->roles as $name => $role_obj ) :
				$role = get_role($name);
				$grant_capability = ( in_array($name, $granted_roles) ) ? true : false;

				/**
				 * Filters the sorting capability for a given role and post type.
				 *
				 * @since 3.1.9
				 *
				 * @param bool  $grant_role     Whether role may sort post type.
				 * @param string $type 			The post type name.
				 * @param string  $role_name	The Role Name.
				 */
				$grant_capability = apply_filters("nestedpages_sort_capability", $grant_capability, $type, $role);

				if ( $grant_capability ) $role->add_cap("nestedpages_sort_$type", true);
			endforeach;
		endforeach;
	}
}