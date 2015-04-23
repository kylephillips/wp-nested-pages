<?php namespace NestedPages\Entities\Confirmation;
/**
* Confirm page(s) moved to trash
*/
class TrashConfirmation implements ConfirmationInterface {

	public function setMessage()
	{
		$out = '';
		$trashed = ( explode(',', $_GET['ids']) );
		if ( count($trashed) > 1 ){
			$out .= count($trashed) . ' ' . __('pages moved to the Trash', 'nestedpages');
		} else {
			$out .= '<strong>' . get_the_title($trashed[0]) . ' </strong>' . __('moved to the Trash', 'nestedpages');

			// Undo Link
			if ( current_user_can('delete_pages') ) {
				$page_obj = get_post_type_object('page');
				$out .= ' <a href="' . wp_nonce_url( admin_url( sprintf( $page_obj->_edit_link . '&amp;action=untrash', $trashed[0] ) ), 'untrash-post_' . $trashed[0] ) . '">' . __( 'Undo' ) . "</a>";
			}
		}
		return $out;
	}

}