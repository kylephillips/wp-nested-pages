<?php
/**
* Modal Form for cloning posts
*/
?>
<div class="np-modal fade nestedpages np-modal-form" id="np-clone-modal">
	<div class="modal-dialog">
		<div class="modal-content clone-modal">
			<h3><?php _e('Clone', 'nestedpages'); ?> <span data-clone-parent></span></h3>

			<div class="modal-body">

			<div class="form-control">
				<label for="clone-quantity"><?php _e('Number of Copies', 'nestedpages'); ?></label>
				<select id="clone-quantity" data-clone-quantity>
					<option value="1" selected="">1</option>
					<?php 
					for ( $i = 2; $i < 11; $i++ ){
						echo '<option value="' . $i . '">' . $i . '</option>';
					}
					?>
				</select>
			</div>
		
			<div class="form-control">
				<label><?php _e( 'Status' ); ?></label>
				<select name="_status" data-clone-status>
				<?php if ( $can_publish ) : ?>
					<option value="publish"><?php _e( 'Published' ); ?></option>
				<?php endif; ?>
					<option value="pending"><?php _e( 'Pending Review' ); ?></option>
					<option value="draft"><?php _e( 'Draft' ); ?></option>
				</select>
			</div>

			<?php
				$authors_dropdown = '';
				if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
					$users_opt = array(
						'hide_if_only_one_author' => false,
						'who' => 'authors',
						'name' => 'post_author',
						'id' => 'post_author',
						'class'=> 'authors',
						'multi' => 1,
						'echo' => 0
					);

					if ( $authors = wp_dropdown_users( $users_opt ) ) :
						$authors_dropdown  = '<div class="form-control" data-clone-author><label>' . __( 'Author' ) . '</label>';
						$authors_dropdown .= $authors;
						$authors_dropdown .= '</div>';
					endif;
					echo $authors_dropdown;
				endif;
			?>
			</div><!-- .modal-body -->
	
		</div><!-- /.modal-content -->
		<div class="modal-footer">
			<button type="button" class="button modal-close" data-dismiss="modal">
				<?php _e('Cancel'); ?>
			</button>
			<a accesskey="s" class="button-primary alignright" data-confirm-clone>
				<?php _e( 'Clone', 'nestedpages' ); ?>
			</a>
			<span class="np-qe-loading np-link-loading" data-clone-loading></span>
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->