<?php
namespace NestedPages\Entities\Confirmation;

/**
* Confirm page(s) restored from trash
*/
class TrashRestoredConfirmation implements ConfirmationInterface
{
	public function setMessage()
	{
		$count = intval(sanitize_text_field($_GET['untrashed']));
		return sprintf( esc_html__( _n( '%d item restored from trash.', '%d items restored from trash.', $count, 'wp-nested-pages'  ) ), $count );
	}
}
