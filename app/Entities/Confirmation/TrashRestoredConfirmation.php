<?php namespace NestedPages\Entities\Confirmation;
/**
* Confirm page(s) restored from trash
*/
class TrashRestoredConfirmation implements ConfirmationInterface {

	public function setMessage()
	{
		$untrashed = sanitize_text_field($_GET['untrashed']);
		$page = ( intval($untrashed) > 1 ) ? __('pages', 'nestedpages') : __('page', 'nestedpages');
		$out = $untrashed . ' ' . $page . ' ' . __('restored from trash', 'nestedpages');
		return $out;
	}

}