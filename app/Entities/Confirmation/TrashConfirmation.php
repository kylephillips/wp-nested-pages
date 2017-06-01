<?php
namespace NestedPages\Entities\Confirmation;

/**
* Confirm page(s) moved to trash
*/
class TrashConfirmation implements ConfirmationInterface
{
	public function setMessage()
	{
		$out = '';

		// Show number of Links (np-redirect) Deleted if applicable
		if ( isset($_GET['link_ids']) ) :
			$links_trashed = ( explode(',', $_GET['link_ids']) );
			$number_trashed = count($links_trashed);
			$out .= ( $number_trashed > 1 )
				? ' ' . $number_trashed . ' ' . __('Links Removed.', 'wp-nested-pages') . ' '
				: ' ' . $number_trashed . ' ' . __('Link Removed.', 'wp-nested-pages') . ' ';
		endif;

		// Post(s) Moved to Trash
		if ( isset($_GET['ids']) ) :
			$trashed = ( explode(',', $_GET['ids']) );
			$post_type = get_post_type($trashed[0]);
			$post_type_object = get_post_type_object($post_type);
			$out .= ( count($trashed) > 1 )
				? count($trashed) . ' ' . esc_html($post_type_object->labels->name) . ' ' . __('moved to the Trash.', 'wp-nested-pages')
				: '<strong>' . get_the_title($trashed[0]) . ' </strong>' . __('moved to the Trash.', 'wp-nested-pages');			
			// Undo Link
			if ( current_user_can('delete_pages') ) {
				$ids = preg_replace( '/[^0-9,]/', '', $_GET['ids'] );
				$out .= ' <a href="' . wp_nonce_url( admin_url( 'edit.php?&post_type=' . esc_attr($post_type) . '&amp;doaction=undo'. '&amp;action=untrash&amp;ids=' . esc_attr($ids) ), 'bulk-posts') . '">' . __( 'Undo' ) . "</a>";
			}
		endif; 

		return $out;
	}
}