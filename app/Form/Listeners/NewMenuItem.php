<?php 

namespace NestedPages\Form\Listeners;

use NestedPages\Helpers;

/**
* Creates new Menu item and saves redirect post
* @return json response
*/
class NewMenuItem extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
	}
}