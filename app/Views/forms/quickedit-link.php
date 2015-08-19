<?php 
	$post_type_object = get_post_type_object( 'np-redirect' );
	$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>

<form method="get" action="">
	<div class="form-interior">
	<h3><?php _e('Link', 'nestedpages'); ?></h3>

	<div class="np-quickedit-error" style="clear:both;display:none;"></div>

	<div class="fields">
	
	<div class="left">
		
		<div class="form-control">
			<label><?php _e( 'Navigation Label' ); ?></label>
			<input type="text" name="post_title" class="np_title" value="" />
		</div>

		<div class="form-control" data-url-field>
			<label><?php _e( 'URL' ); ?></label>
			<input type="text" name="post_content" class="np_content" value="" />
		</div>

		<div class="form-control">
			<label><?php _e( 'Status' ); ?></label>
			<select name="_status" class="np_status">
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

		<?php if ( $this->user->canSortPages() && !$this->isSearch() ) : // Menu Options Button ?>
		<div class="form-control">
			<label><?php _e( 'Title Attribute' ); ?></label>
			<input type="text" name="titleAttribute" class="np_title_attribute" value="" />
		</div>
		<div class="form-control">
			<label><?php _e( 'CSS Classes' ); ?></label>
			<input type="text" name="cssClasses" class="np_nav_css_classes" value="" />
		</div>
		<div class="comments">
			<label>
				<input type="checkbox" name="nav_status" class="np_nav_status" value="hide" />
				<span class="checkbox-title"><?php _e( 'Hide in Nav Menu', 'nestedpages' ); ?></span>
			</label>
		</div>
		<div class="comments">
			<label>
				<input type="checkbox" name="nested_pages_status" class="np_status" value="hide" />
				<span class="checkbox-title"><?php _e( 'Hide in Nested Pages', 'nestedpages' ); ?></span>
			</label>
		</div>
		<div class="comments">
			<label>
				<input type="checkbox" name="linkTarget" class="link_target" value="_blank" />
				<span class="checkbox-title"><?php _e( 'Open link in a new window/tab' ); ?></span>
			</label>
		</div>			
		<?php endif; // Edit theme options?>

	</div><!-- .right -->

	</div><!-- .fields -->

	</div><!-- .form-interior -->

	<div class="buttons">
		<input type="hidden" name="objectType" data-np-menu-object-input value="">
		<input type="hidden" name="objectId" data-np-menu-objectid-input value="">
		<input type="hidden" name="menuType" data-np-menu-type-input value="">
		<input type="hidden" name="post_id" class="np_id" value="<?php echo get_the_id(); ?>">
		<input type="hidden" name="parent_id" class="np_parent_id" value="">
		<a accesskey="c" href="#inline-edit" class="button-secondary alignleft np-cancel-quickedit">
			<?php _e( 'Cancel' ); ?>
		</a>
		<a accesskey="s" href="#inline-edit" class="button-primary np-save-quickedit-redirect alignright">
			<?php _e( 'Update' ); ?>
		</a>
		<span class="np-qe-loading"></span>
	</div>
</form>