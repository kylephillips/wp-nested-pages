<?php
/**
* Modal Form for adding a new link
*/
$post_type_object = get_post_type_object( 'page' );
$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>
<div class="modal fade" id="np-link-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="get" action="" class="np-modal-form np-new-link-form">
			<div class="modal-header">
				<h4 class="modal-title" id="np-add-link-title"><?php _e('Add Link', 'nestedpages'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="form-interior">

				<div class="np-quickedit-error np-new-link-error" style="clear:both;display:none;"></div>
					
				<div class="left">

				<div class="form-control">
					<label><?php _e( 'Menu Title' ); ?></label>
					<input type="text" name="np_link_title" class="np_link_title" value="" />
				</div>

				<div class="form-control">
					<label><?php _e( 'URL' ); ?></label>
					<input type="text" name="np_link_content" class="np_link_content" value="" />
				</div>

				<div class="form-control">
					<label><?php _e( 'Status' ); ?></label>
					<select name="_status" class="np_link_status">
					<?php if ( $can_publish ) : ?>
						<option value="publish"><?php _e( 'Published' ); ?></option>
						<option value="future"><?php _e( 'Scheduled' ); ?></option>
					<?php endif; ?>
						<option value="pending"><?php _e( 'Pending Review' ); ?></option>
						<option value="draft"><?php _e( 'Draft' ); ?></option>
					</select>
				</div>

				</div><!-- .left -->

				<div class="right">

				<?php if ( current_user_can('edit_theme_options') ) : ?>
				<label class="checkbox">
					<input type="checkbox" name="nav_status" class="np_link_nav_status" value="hide" />
					<span class="checkbox-title"><?php _e( 'Hide in Nav Menu', 'nestedpages' ); ?></span>
				</label>

				<label class="checkbox">
					<input type="checkbox" name="nested_pages_status" class="np_link_status" value="hide" />
					<span class="checkbox-title"><?php _e( 'Hide in Nested Pages', 'nestedpages' ); ?></span>
				</label>

				<label class="checkbox">
					<input type="checkbox" name="link_target" class="new_link_target" value="_blank" />
					<span class="checkbox-title"><?php _e( 'Open link in new window', 'nestedpages' ); ?></span>
				</label>
				<?php endif; // Edit theme options ?>

				</div><!-- .right -->

				</div><!-- .form-interior -->
			</div>
			<div class="modal-footer">
				<input type="hidden" name="parent_id" class="parent_id" value="">
				<button type="button" class="button modal-close" data-dismiss="modal">Close</button>
				<a accesskey="s" class="button-primary np-save-link alignright">
					<?php _e( 'Save Link', 'nestedpages' ); ?>
				</a>
				<span class="np-qe-loading np-link-loading"></span>
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->