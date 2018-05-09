<div class="nestedpages-list-header">
	<div class="np-check-all">
		<input type="checkbox" data-np-check-all="nestedpages_bulk[]" data-np-bulk-checkbox />
	</div>
	<form data-np-bulk-form style="display:none;" action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="np-bulk-form">
		<input type="hidden" name="action" value="npBulkActions">
		<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
		<input type="hidden" name="redirect_post_ids" value="" data-np-bulk-redirect-ids>
		<input type="hidden" name="post_ids" value="" data-np-bulk-ids>
		<select id="np_bulk" name="np_bulk_action" class="nestedpages-sort">
			<option value="no-action"><?php _e('Bulk Actions', 'wp-nested-pages'); ?></option>
			<?php if ( current_user_can('delete_pages') ) : ?>
			<option value="trash"><?php _e('Move to Trash', 'wp-nested-pages'); ?></option>
			<?php endif; ?>
			<option value="edit"><?php _e('Edit', 'wp-nested-pages'); ?></option>
		</select>
		<input type="submit" class="button" value="<?php echo esc_attr__('Apply', 'wp-nested-pages'); ?>">
	</form>
	<p class="np-hidden-select-count" data-np-hidden-count-parent><span data-np-hidden-count></span> <?php _e('Nested Items Selected', 'wp-nested-pages'); ?>. <a href="#" class="nestedpages-toggleall"><?php _e('Expand All', 'wp-nested-pages'); ?></a></p>
</div>