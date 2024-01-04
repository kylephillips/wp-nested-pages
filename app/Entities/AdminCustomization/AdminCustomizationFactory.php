<?php 
namespace NestedPages\Entities\AdminCustomization;

use NestedPages\Entities\AdminCustomization\AdminMenuItems;
use NestedPages\Entities\AdminCustomization\AdminMenuSanitization;

/**
* Initialize Admin Customizations
*/
class AdminCustomizationFactory 
{	
	public function __construct()
	{
		new AdminMenuItems;
		new AdminMenuSanitization;
	}
}