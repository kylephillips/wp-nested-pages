<?php
namespace NestedPages\Entities\Confirmation;

/**
* Confirm page(s) restored from trash
*/
class TrashRestoredConfirmation implements ConfirmationInterface
{
	public function setMessage()
	{
		$untrashed = sanitize_text_field($_GET['untrashed']);
		$label = ( intval($untrashed) > 1 ) ? __('items', 'wp-nested-pages') : __('item', 'wp-nested-pages');
		$out = $untrashed . ' ' . $label . ' ' . __('restored from trash.', 'wp-nested-pages');
		return $out;
	}
}
