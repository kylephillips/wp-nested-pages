<?php
namespace NestedPages\Entities\NavMenu;

use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* Edits/Corrections to the generated nav menu on the front-end
*/
class NavMenuFrontEnd
{
	/**
	* Nav Menu Repository
	*/
	private $nav_menu_repo;

	public function __construct()
	{
		$this->nav_menu_repo = new NavMenuRepository;
		add_filter('nav_menu_link_attributes', [$this, 'attributeFilter'], 10, 3);
	}

	/**
	* Filter the link attributes on the generated menu
	*/
	public function attributeFilter($atts, $item, $args)
	{
		if ( $this->nav_menu_repo->getMenuID() == null ) return $atts;
		if ( !isset($args->menu->term_id) ) return $atts;
		if ( $args->menu->term_id !== $this->nav_menu_repo->getMenuID() ) return $atts;

		// Remove the rel= attribute created from saving the menu object for syncing
		foreach($atts as $attribute => $value){
			if ( strtolower($attribute) != 'rel' ) continue;
			if ( $value == $item->object ) unset($atts[$attribute]);
			if ( is_numeric($value) ) unset($atts[$attribute]);
		}
		
		return $atts;
	}
}