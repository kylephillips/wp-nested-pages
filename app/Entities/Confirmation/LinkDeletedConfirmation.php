<?php
namespace NestedPages\Entities\Confirmation;

/**
* Confirm Link has been deleted
*/
class LinkDeletedConfirmation implements ConfirmationInterface 
{
	public function setMessage()
	{
		$out = __('Link successfully deleted.', 'wp-nested-pages');
		return $out;
	}
}