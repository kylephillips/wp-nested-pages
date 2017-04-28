<?php
/**
* Modal for confirming link delete
*/
?>
<div class="np-modal fade np-trash-modal" id="np-delete-confirmation-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p data-np-link-delete-text></p>
				<a href="#" class="np-cancel-trash button modal-close" data-dismiss="modal"><?php _e('Cancel', 'nestedpages'); ?></a>
				<a href="#" class="button-primary" data-delete-confirmation><?php _e('Delete', 'nestedpages'); ?></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->