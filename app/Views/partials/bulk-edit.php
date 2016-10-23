<?php 
	$post_type_object = get_post_type_object( 'page' );
	$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>
<form data-np-bulk-edit-form class="nestedpages-bulk-edit" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
	<input type="hidden" name="action" value="npBulkEdit">
	<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">

	<h3><?php _e('Bulk Edit', 'nestedpages'); ?></h3>
	
	<ul class="np-bulk-titles" data-np-bulk-titles></ul>
	
	<div class="quick-edit">
		<div class="fields">
			<div class="left">
				<?php 
				$authors_dropdown = '';
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
					$users_opt = array(
						'show_option_none' => '&mdash; ' . __('No Change') . ' &mdash;',
						'hide_if_only_one_author' => false,
						'who' => 'authors',
						'name' => 'post_author',
						'id' => 'post_author',
						'class'=> 'authors',
						'multi' => 1,
						'echo' => 0
					);
					if ( $authors = wp_dropdown_users( $users_opt ) ) :
						$authors_dropdown  = '<div class="form-control"><label>' . __( 'Author' ) . '</label>';
						$authors_dropdown .= $authors;
						$authors_dropdown .= '</div>';
					endif;
					echo $authors_dropdown;
				endif;
				?>

				<div class="form-control">
					<label><?php _e( 'Status' ); ?></label>
					<select name="_status">
						<option value="">&mdash; <?php _e('No Change'); ?> &mdash;</option>
					<?php if ( $can_publish ) : ?>
						<option value="publish"><?php _e( 'Published' ); ?></option>
					<?php endif; ?>
						<option value="private"><?php _e( 'Private' ); ?></option>
						<option value="pending"><?php _e( 'Pending Review' ); ?></option>
						<option value="draft"><?php _e( 'Draft' ); ?></option>
					</select>
				</div>

			</div><!-- .left -->

			<div class="right">
				
				<?php if ( $this->post_type->hierarchical ) : ?>
				<div class="form-control">
					<label><?php _e( 'Template' ); ?></label>
					<select name="page_template">
						<option value="">&mdash; <?php _e('No Change'); ?> &mdash;</option>
						<option value="default"><?php _e( 'Default Template' ); ?></option>
						<?php page_template_dropdown() ?>
					</select>
				</div>
				<?php endif; ?>

				<div class="form-control">
					<label><?php _e( 'Comments' ); ?></label>
					<select name="comment_status">
						<option value="">&mdash; <?php _e('No Change'); ?> &mdash;</option>
						<option value="open"><?php _e('Allow'); ?></option>
						<option value="closed"><?php _e('Do not allow'); ?></option>
					</select>
				</div>
				
				<?php if ( current_user_can('edit_theme_options') ) : ?>
				<div class="form-control">
					<label><?php _e( 'Display in Nested View', 'nestedpages' ); ?></label>
					<select name="nested_pages_status">
						<option value="">&mdash; <?php _e('No Change'); ?> &mdash;</option>
						<option value="hide"><?php _e('Hide'); ?></option>
						<option value="show"><?php _e('Show'); ?></option>
					</select>
				</div>
				<?php endif; // Edit theme options ?>

			</div><!-- .right -->
		</div><!-- .fields -->

		<div class="np-bulk-footer">
			<button class="button pull-left" data-np-cancel-bulk-edit><?php _e('Cancel', 'nestedpages'); ?></button>
			<button type="submit" class="button button-primary"><?php _e('Update', 'nestedpages'); ?></button>
		</div><!-- .np-bulk-footer -->

	</div><!--.quickedit -->
</form><!-- .nestedpages-bulk-edit -->