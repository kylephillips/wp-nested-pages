<?php 
namespace NestedPages\Entities\AdminCustomization;

use NestedPages\Entities\AdminCustomization\AdminMenuItems;

/**
* Initialize Admin Customizations
*/
class AdminCustomizationFactory 
{	
	public function __construct()
	{
		new AdminMenuItems;
	}
}