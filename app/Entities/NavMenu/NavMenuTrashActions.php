<?php 

namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* Performs necessary actions when a menu item is trashed
*/
class NavMenuTrashActions 
{

	/**
	* Nav Menu Repository
	*/
	private $nav_menu_repo;

	public function __construct()
	{
		$this->nav_menu_repo = new NavMenuRepository;
		add_action( 'before_delete_post', array( $this, 'removeLinkItem'), 10 );
		add_action( 'before_delete_post', array( $this, 'hidePagefromNav'), 10 );
	}

	/**
	* Remove Link Post (np-redirect) when a link in the menu is removed
	*/
	public function removeLinkItem($post_id)
	{	
		remove_action( 'before_delete_post', array( $this, 'removeLinkItem'), 10 );
		$redirect_id = get_post_meta($post_id, '_menu_item_xfn', true);
		if ( $redirect_id !== "" ) wp_delete_post($redirect_id, true);
		return true;
	}

	/**
	* Set Page items to hide from nav if page nav item is deleted
	*/
	public function hidePagefromNav($post_id)
	{
		if ( get_post_type($post_id) == 'nav_menu_item' ){
			
		}
	}

}