<?php
/**
* Modal for confirming link delete
*/
?>
<div class="nestedpages-modal-backdrop" data-nestedpages-modal="np-delete-confirmation-modal"></div>
<div class="nestedpages-modal-content short small <?php if ( $this->integrations->plugins->dark_mode->installed ) echo 'np-dark-mode'; ?>" id="np-trash-modal" data-nestedpages-modal="np-delete-confirmation-modal">
	<div class="modal-body np-trash-modal">
		<p data-np-link-delete-text></p>
		<a href="#" class="np-cancel-trash button modal-close" data-nestedpages-modal-close><?php _e('Cancel', 'wp-nested-pages'); ?></a>
		<a href="#" class="button-primary" data-delete-confirmation><?php _e('Delete', 'wp-nested-pages'); ?></a>
	</div>
</div><!-- /.modal -->