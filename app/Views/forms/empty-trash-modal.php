<?php
/**
* Modal for confirming trash empty
*/
?>
<div class="np-modal fade np-trash-modal" id="np-trash-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p>Are you sure you would like to empty the trash? This action is not reversable.</p>
				<a href="#" class="np-cancel-trash button modal-close" data-dismiss="modal"><?php _e('Cancel', 'nestedpages'); ?></a>
				<a href="#" class="np-trash-confirm button-primary"><?php _e('Empty Trash', 'nestedpages'); ?></a>
				<input type="hidden" id="np-trash-posttype" value="<?php echo $this->post_type->name; ?>">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->