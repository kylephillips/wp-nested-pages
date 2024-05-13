<?php 
$post_type_object = get_post_type_object( $this->post_type->name );
$can_publish = current_user_can( $post_type_object->cap->publish_posts );
$wpml_pages = ( $this->integrations->plugins->wpml->installed && $this->integrations->plugins->wpml->isDefaultLanguage()) ? true : false;
$has_menu_options = ( $this->user->canSortPosts($this->post_type->name) && $this->post_type->name == 'page' && !$this->listing_repo->isSearch() && !$this->settings->menusDisabled()  ) ? true : false;
?>
<form data-np-bulk-edit-form class="nestedpages-bulk-edit" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
	<input type="hidden" name="action" value="npBulkEdit">
	<?php wp_nonce_field('nestedpages-nonce', 'nonce'); ?>
	<input type="hidden" name="page" value="<?php echo $this->pageURL(); ?>">
	<input type="hidden" name="post_type" value="<?php echo $this->post_type->name; ?>">

	<h3><?php _e('Bulk Edit', 'wp-nested-pages'); ?></h3>

	<div class="np-bulk-edit-link-info">
		<div class="np-quickedit-info" data-bulk-edit-link-count><?php _e('There are links selected. Bulk edit will not apply to links.', 'wp-nested-pages'); ?></div>
	</div>
	
	<ul class="np-bulk-titles" data-np-bulk-titles></ul>
	
	<div class="quick-edit">
		<div class="fields">
			<div class="left">
				<?php 
				$authors_dropdown = '';
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
					$users_opt = [
						'show_option_none' => '&mdash; ' . __('No Change', 'wp-nested-pages') . ' &mdash;',
						'hide_if_only_one_author' => false,
						'capability' => 'edit_posts',
						'name' => 'post_author',
						'id' => 'post_author',
						'class'=> 'authors',
						'multi' => 1,
						'echo' => 0
					];
					if ( $authors = wp_dropdown_users( $users_opt ) ) :
						$authors_dropdown  = '<div class="form-control"><label>' . __( 'Author', 'wp-nested-pages') . '</label>';
						$authors_dropdown .= $authors;
						$authors_dropdown .= '</div>';
					endif;
					echo $authors_dropdown;
				endif;
				?>

				<div class="form-control">
					<label><?php _e( 'Status', 'wp-nested-pages' ); ?></label>
					<select name="_status">
						<option value="">&mdash; <?php _e('No Change', 'wp-nested-pages'); ?> &mdash;</option>
					<?php if ( $can_publish ) : ?>
						<option value="publish"><?php _e( 'Published', 'wp-nested-pages' ); ?></option>
					<?php endif; ?>
						<option value="private"><?php _e( 'Private', 'wp-nested-pages' ); ?></option>
						<option value="pending"><?php _e( 'Pending Review', 'wp-nested-pages' ); ?></option>
						<option value="draft"><?php _e( 'Draft', 'wp-nested-pages' ); ?></option>
					</select>
				</div>

				<?php if ( $this->post_type->hierarchical ) : ?>
				<div class="form-control">
					<label><?php _e( 'Template', 'wp-nested-pages' ); ?></label>
					<select name="page_template">
						<option value="">&mdash; <?php _e('No Change', 'wp-nested-pages'); ?> &mdash;</option>
						<option value="default"><?php _e( 'Default Template', 'wp-nested-pages' ); ?></option>
						<?php 
              if( is_page() ){
                page_template_dropdown();
              }else{
                page_template_dropdown('', $this->post_type->name);
              }
            ?>
					</select>
				</div>
				<?php endif; ?>

				<?php if ( $this->user->canSortPosts($this->post_type->name) && $this->post_type->hierarchical ) : ?>
				<div class="form-control">
					<label><?php echo sprintf(__('Parent %s', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></label>
					<?php 
						wp_dropdown_pages([
							'show_option_no_change'=> __('— No Change —', 'wp-nested-pages'),
							'sort_column' => 'menu_order', 
							'hierarchical' => 1,
							'depth' => 0,
							'name' => 'post_parent',
							'post_type' => $this->post_type->name
						]);
					?>
				</div>
				<?php endif; ?>

				<?php
				$custom_fields_left = $this->custom_fields_repo->outputBulkEditFields($this->post_type, 'left');
				if ( $custom_fields_left ) echo $custom_fields_left;
				?>

			</div><!-- .left -->

			<div class="right">

				<div class="form-control">
					<label><?php _e( 'Comments', 'wp-nested-pages' ); ?></label>
					<select name="comment_status">
						<option value="">&mdash; <?php _e('No Change', 'wp-nested-pages'); ?> &mdash;</option>
						<option value="open"><?php _e('Allow', 'wp-nested-pages'); ?></option>
						<option value="closed"><?php _e('Do not allow', 'wp-nested-pages'); ?></option>
					</select>
				</div>
				
				<?php if ( current_user_can('edit_theme_options') && !array_key_exists('hide_in_np', $this->disabled_standard_fields) ) : ?>
				<div class="form-control">
					<label><?php _e( 'Display in Nested View', 'wp-nested-pages' ); ?></label>
					<select name="nested_pages_status">
						<option value="">&mdash; <?php _e('No Change', 'wp-nested-pages'); ?> &mdash;</option>
						<option value="hide"><?php _e('Hide', 'wp-nested-pages'); ?></option>
						<option value="show"><?php _e('Show', 'wp-nested-pages'); ?></option>
					</select>
				</div>

				<?php if ( $this->user->canSortPosts($this->post_type->name) && $has_menu_options ) : ?>
				<div class="form-control">
					<label><?php _e( 'Hide in Nav Menu', 'wp-nested-pages' ); ?></label>
					<select name="nav_status">
						<option value="">&mdash; <?php _e('No Change', 'wp-nested-pages'); ?> &mdash;</option>
						<option value="hide"><?php _e('Hide', 'wp-nested-pages'); ?></option>
						<option value="show"><?php _e('Show', 'wp-nested-pages'); ?></option>
					</select>
				</div>
				<?php endif; endif; // Edit theme options ?>

				<?php
				$custom_fields_right = $this->custom_fields_repo->outputBulkEditFields($this->post_type, 'right');
				if ( $custom_fields_right ) echo $custom_fields_right;
				?>

			</div><!-- .right -->
		</div><!-- .fields -->

		<?php if ( !empty($this->h_taxonomies) || !empty($this->f_taxonomies)) : ?>
		<div class="np-taxonomies">
			<?php foreach ( $this->h_taxonomies as $taxonomy ) : ?>
			<div class="np-taxonomy">
				<span class="title"><?php esc_html_e( $taxonomy->labels->name ) ?></span>
				<input type="hidden" name="<?php echo ( $taxonomy->name == 'category' ) ? 'post_category[]' : 'tax_input[' . esc_attr( $taxonomy->name ) . '][]'; ?>" value="0" />
				<ul class="cat-checklist <?php esc_attr_e( $taxonomy->name )?>-checklist">
					<?php wp_terms_checklist( null, array( 'taxonomy' => $taxonomy->name ) ) ?>
				</ul>
			</div><!-- .np-taxonomy -->
			<?php endforeach; ?>

			<?php foreach ( $this->f_taxonomies as $taxonomy ) : ?>
			<div class="np-taxonomy">
				<span class="title"><?php esc_html_e( $taxonomy->labels->name ) ?></span>
				<textarea id="<?php esc_attr_e($taxonomy->name); ?>" cols="22" rows="1" name="tax_input[<?php esc_attr_e( $taxonomy->name )?>]" class="tax_input_<?php esc_attr_e( $taxonomy->name )?>" data-autotag data-taxonomy="<?php esc_attr_e($taxonomy->name); ?>"></textarea>
			</div><!-- .np-taxonomy -->
			<?php endforeach; ?>
		</div><!-- .taxonomies -->
		<?php endif; // if taxonomies ?>

		<div class="np-bulk-footer">
			<button class="button pull-left" data-np-cancel-bulk-edit>
				<?php _e('Cancel', 'wp-nested-pages'); ?>
			</button>
			<button type="submit" class="button button-primary">
				<?php _e('Update', 'wp-nested-pages'); ?>
			</button>
		</div><!-- .np-bulk-footer -->

	</div><!--.quickedit -->
</form><!-- .nestedpages-bulk-edit -->