<?php
/**
* Modal for confirming trash empty
*/
?>
<div class="nestedpages-modal-backdrop" data-nestedpages-modal="np-trash-modal"></div>
<div class="nestedpages-modal-content small short <?php if ( $this->integrations->plugins->dark_mode->installed ) echo 'np-dark-mode'; ?>" id="np-trash-modal" data-nestedpages-modal="np-trash-modal">
	<div class="modal-body np-trash-modal">
		<p><?php _e('Are you sure you would like to empty the trash? This action is not reversable.', 'wp-nested-pages'); ?></p>
		<a href="#" class="np-cancel-trash button modal-close" data-nestedpages-modal-close><?php _e('Cancel', 'wp-nested-pages'); ?></a>
		<a href="#" class="np-trash-confirm button-primary"><?php _e('Empty Trash', 'wp-nested-pages'); ?></a>
		<input type="hidden" id="np-trash-posttype" value="<?php echo esc_attr($this->post_type->name); ?>">
	</div>
</div><!-- /.modal -->