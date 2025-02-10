<?php 
	$post_type_object = get_post_type_object( 'page' );
	$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>
<form method="get" action="" class="np-new-child-form">
	<div class="form-interior">
	<h3 data-new-post-relation-title><strong><?php _e('Add Child', 'wp-nested-pages'); ?></strong><span class="parent_name"></span></h3>

	<div class="np-quickedit-error" style="clear:both;display:none;"></div>
	
	<div class="fields">
	
	<div class="left">

		<ol class="new-page-titles">
			<li>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
				<div class="form-control new-child-row">
					<label><?php _e( 'Title' ); ?></label>
					<div>
						<input type="text" name="post_title[]" class="np_title" placeholder="<?php _e('Title', 'wp-nested-pages'); ?>" value="" tabindex="1" />
						<a href="#" class="button-secondary np-remove-child">-</a>
					</div>
				</div>
			</li>
		</ol>

		<a href="#" class="add-new-child-row button-primary" style="clear:both;"><?php _e('+', 'wp-nested-pages'); ?></a>
	</div><!-- .left -->


	<div class="right">
		
		<div class="form-control">
			<label><?php _e( 'Status' ); ?></label>
			<select name="_status" class="np_status">
			<?php if ( $can_publish ) : ?>
				<option value="publish"><?php _e( 'Published' ); ?></option>
			<?php endif; ?>
				<option value="draft"><?php _e( 'Draft' ); ?></option>
			</select>
		</div>

		<?php 
		/*
		* Authors Dropdown
		*/
		$authors_dropdown = '';
		if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) :
			$users_opt = [
				'hide_if_only_one_author' => false,
				'capability' => 'edit_posts',
				'name' => 'post_author',
				'id' => 'post_author',
				'class'=> 'authors',
				'multi' => 1,
				'echo' => 0,
				'selected' => get_current_user_id()
			];

			if ( $authors = wp_dropdown_users( $users_opt ) ) :
				$authors_dropdown  = '<div class="form-control np_author"><label>' . __( 'Author' ) . '</label>';
				$authors_dropdown .= $authors;
				$authors_dropdown .= '</div>';
			endif;
			echo $authors_dropdown;
		endif;
		?>

		<?php if ( $this->post_type->hierarchical ) : ?>
		<div class="form-control">
			<label><?php _e( 'Template' ); ?></label>
			<select name="page_template" class="np_template">
				<option value="default"><?php _e( 'Default Template' ); ?></option>
				<?php
				if ( is_page() ){
					page_template_dropdown();
				} else {
					page_template_dropdown('', $this->post_type->name);
				}
				?>
			</select>
		</div>
		<?php endif; ?>

		<?php if ( $this->post_type->name == 'page' && $this->user->canSortPosts($this->post_type->name) && !$this->listing_repo->isSearch() ) : ?>
		<div class="form-control full checkbox">
			<label>
				<input type="checkbox" name="nav_status" class="np_nav_status" value="hide" <?php if ( $this->settings->defaultHideInNav() ) echo 'checked'; ?> />
				<span class="checkbox-title"><?php _e( 'Hide in Nav Menu', 'wp-nested-pages' ); ?></span>
			</label>
		</div>
		<?php endif; ?>

	</div><!-- .right -->

	</div><!-- .fields -->

	</div><!-- .form-interior -->


	<div class="buttons">
		<div class="buttons-inner">
		<input type="hidden" name="parent_id" class="page_parent_id" />
		<input type="hidden" name="before_id" class="page_before_id" />
		<input type="hidden" name="after_id" class="page_after_id" />
		<input type="hidden" name="post_type" value="<?php echo $this->post_type->name; ?>" />
		<a accesskey="c" href="#" class="button-secondary alignleft np-cancel-newchild" data-nestedpages-modal-close>
			<?php _e( 'Cancel' ); ?>
		</a>
		<a accesskey="s" href="#" class="button-primary np-save-newchild alignright" style="margin-left:10px;">
			<?php _e( 'Add', 'wp-nested-pages' ); ?>
		</a>
		<a href="#" class="button-secondary np-save-newchild add-edit alignright">
			<?php _e( 'Add & Edit', 'wp-nested-pages' ); ?>
		</a>
		<div class="np-qe-loading">
			<?php include( NestedPages\Helpers::asset('images/spinner.svg') ); ?>
		</div>
		</div>
	</div>
</form>
