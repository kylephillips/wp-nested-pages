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
			$links_trashed = ( explode(',', sanitize_text_field($_GET['link_ids'])) );
			$number_trashed = count($links_trashed);
			$out .= sprintf( esc_html( _n( '%d Link Removed.', '%d Links Removed.', $number_trashed, 'wp-nested-pages'  ) ), $number_trashed );
		endif;

		// Post(s) Moved to Trash
		if ( isset($_GET['ids']) ) :
			$trashed = ( explode(',', sanitize_text_field($_GET['ids'])) );
			$post_type = get_post_type($trashed[0]);
			$post_type_object = get_post_type_object($post_type);
			$out .= ( count($trashed) > 1 )
				? sprintf(__('%d %s moved to the trash.'), count($trashed), esc_html($post_type_object->labels->name))
				: wp_kses(sprintf(__('<strong>%s</strong> moved to the trash.', 'wp-nested-pages'), get_the_title($trashed[0])), ['strong' => []] );
			// Undo Link
			if ( current_user_can('delete_pages') ) {
				$ids = preg_replace( '/[^0-9,]/', '', sanitize_text_field($_GET['ids']) );
				$out .= ' <a href="' . wp_nonce_url( admin_url( 'edit.php?&post_type=' . esc_attr($post_type) . '&amp;doaction=undo'. '&amp;action=untrash&amp;ids=' . esc_attr($ids) ), 'bulk-posts') . '">' . __( 'Undo' ) . "</a>";
			}
		endif; 

		return $out;
	}
}