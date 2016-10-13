<?php
$page_obj = get_post_type_object('page');
?>
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
			<option value="no-action"><?php _e('Bulk Actions', 'nestedpages'); ?></option>
			<?php if ( current_user_can('delete_pages') ) : ?>
			<option value="trash"><?php _e('Move to Trash', 'nestedpages'); ?></option>
			<?php endif; ?>
		</select>
		<input type="submit" class="button" value="Apply">
	</form>
	<p class="np-hidden-select-count" data-np-hidden-count-parent><span data-np-hidden-count></span> <?php _e('Nested Items Selected', 'nestedpages'); ?>. <a href="#" class="nestedpages-toggleall"><?php _e('Expand All', 'nestedpages'); ?></a></p>
</div>