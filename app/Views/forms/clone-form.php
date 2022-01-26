<?php
/**
* Modal Form for cloning posts
*/
?>
<div class="nestedpages-modal-backdrop" data-nestedpages-modal="np-clone-modal"></div>
<div class="nestedpages-modal-content small short <?php if ( $this->integrations->plugins->dark_mode->installed ) echo 'np-dark-mode'; ?>" id="np-clone-modal" data-nestedpages-modal="np-clone-modal">

	<div class="modal-content clone-modal np-modal-form <?php if ( $this->integrations->plugins->dark_mode->installed ) echo 'np-dark-mode'; ?>">
		<h3><?php _e('Clone', 'wp-nested-pages'); ?> <span data-clone-parent></span></h3>

		<div class="modal-body">

		<div class="form-control">
			<label for="clone-quantity"><?php _e('Number of Copies', 'wp-nested-pages'); ?></label>
			<select id="clone-quantity" data-clone-quantity>
				<option value="1" selected="">1</option>
				<?php 
				for ( $i = 2; $i < 11; $i++ ){
					echo '<option value="' . absint($i) . '">' . absint($i) . '</option>';
				}
				?>
			</select>
		</div>
	
		<div class="form-control">
			<label><?php _e( 'Status', 'wp-nested-pages' ); ?></label>
			<select name="_status" data-clone-status>
			<?php if ( $can_publish ) : ?>
				<option value="publish"><?php _e( 'Published', 'wp-nested-pages' ); ?></option>
			<?php endif; ?>
				<option value="pending"><?php _e( 'Pending Review', 'wp-nested-pages' ); ?></option>
				<option value="draft"><?php _e( 'Draft', 'wp-nested-pages' ); ?></option>
			</select>
		</div>

		<?php
			$authors_dropdown = '';
			if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
				$users_opt = [
					'hide_if_only_one_author' => false,
					'capability' => 'edit_posts',
					'name' => 'post_author',
					'id' => 'post_author',
					'class'=> 'authors',
					'multi' => 1,
					'echo' => 0
				];

				if ( $authors = wp_dropdown_users( $users_opt ) ) :
					$authors_dropdown  = '<div class="form-control" data-clone-author><label>' . __( 'Author' ) . '</label>';
					$authors_dropdown .= $authors;
					$authors_dropdown .= '</div>';
				endif;
				echo $authors_dropdown;
			endif;
		?>
		</div><!-- .modal-body -->

	
		<div class="modal-footer">
			<button type="button" class="button modal-close" data-nestedpages-modal-close>
				<?php _e('Cancel', 'wp-nested-pages'); ?>
			</button>
			<a accesskey="s" class="button-primary alignright" data-confirm-clone>
				<?php _e( 'Clone', 'wp-nested-pages' ); ?>
			</a>
			<div class="np-qe-loading" data-clone-loading>
				<?php include( NestedPages\Helpers::asset('images/spinner.svg') ); ?>
			</div>
		</div>
	</div><!-- .modal-content -->
	
</div><!-- /.modal -->