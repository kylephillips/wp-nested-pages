<?php
namespace NestedPages\Entities\NavMenu;

/**
* Service Class for removing a single menu item
*/
class NavMenuRemoveItem 
{
	/**
	* Item ID to Remove
	* @var int - ID of nav menu item
	*/
	private $item_id;

	public function __construct($item_id)
	{
		$this->item_id = $item_id;
		$this->removeItem();
	}

	/**
	* Delete the Item
	*/
	private function removeItem()
	{
		wp_delete_post($this->item_id, true);
	}
}