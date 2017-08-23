<?php
/**
* Redirect Page
*/
$link = ( $this->post->nav_type && $this->post->nav_type !== 'custom' ) 
	? $this->post->nav_original_link
	: esc_url($this->post->content);
?>
<div class="row" <?php if ( $this->listing_repo->isSearch() ) echo 'style="padding-left:10px;"';?>>
	
	<?php if ( $this->post_type->hierarchical && !$this->listing_repo->isSearch() ) : ?>
	<div class="child-toggle"></div>
	<?php endif; ?>

	<div class="row-inner">

		<i class="np-icon-sub-menu"></i>
		
		<?php if ( $this->user->canSortPages() && !$this->listing_repo->isSearch() ) : ?>
		<i class="handle np-icon-menu"></i>
		<?php endif; ?>

		<a href="<?php echo $link; ?>" class="page-link page-title" target="_blank">
			<span class="title"><?php echo apply_filters('the_title', $this->post->title, $this->post->id, $view = 'nestedpages_title'); ?> <i class="np-icon-link"></i></span>
			<?php 

				// Post Status
				if ( $this->post->status !== 'publish' ){
					echo '<span class="status">(' . __(ucfirst($this->post->status)) . ')</span>';
				} else {
					echo '<span class="status"></span>';
				}

				// Nested Pages Status
				if ( $this->post->np_status == 'hide' )
					echo '<i class="np-icon-eye-blocked"></i>';

				// Nav Status
				if ( $this->post->nav_status == 'hide' ){
					echo '<span class="nav-status">(' . __('Hidden', 'wp-nested-pages') . ')</span>';
				} else {
					echo '<span class="nav-status"></span>';
				}
			?>
		</a>

		<a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a>

		<div class="action-buttons">

			<a href="#" 
				class="np-btn np-quick-edit-redirect" 
				data-id="<?php echo esc_attr($this->post->id); ?>" 
				data-parentid="<?php echo esc_attr($this->post->parent_id); ?>"
				data-title="<?php echo esc_attr($this->post->title); ?>" 
				data-url="<?php echo esc_attr(NestedPages\Helpers::check_url($this->post->content)); ?>"
				data-status="<?php echo esc_attr($this->post->status); ?>" 
				data-np-status="<?php echo esc_attr($this->post->np_status); ?>"
				data-navstatus="<?php echo esc_attr($this->post->nav_status); ?>"
				data-navtitleattr="<?php echo esc_attr($this->post->nav_title_attr); ?>"
				data-navcss="<?php echo esc_attr($this->post->nav_css); ?>"
				data-nav-type="<?php echo esc_attr($this->post->nav_type); ?>"
				data-nav-object="<?php echo esc_attr($this->post->nav_object); ?>"
				data-nav-object-id="<?php echo esc_attr($this->post->nav_object_id); ?>"
				data-nav-original-link="<?php echo esc_attr($this->post->nav_original_link); ?>"
				data-nav-original-title="<?php echo esc_attr($this->post->nav_original_title); ?>"
				data-linktarget="<?php echo esc_attr($this->post->link_target); ?>">
				<?php _e('Quick Edit'); ?>
			</a>

			<?php if ( current_user_can('delete_pages') ) : ?>
			<a href="<?php echo get_delete_post_link($this->post->id, '', true); ?>" class="np-btn np-btn-trash" data-np-confirm-delete>
				<i class="np-icon-remove"></i>
			</a>
			<?php endif; ?>

		</div><!-- .action-buttons -->
	</div><!-- .row-inner -->

	<?php
	// Thumbnail Displays for original post if it's a relational link, otherwise a placeholder displays
	$thumbnail_source = $this->post_type_repo->thumbnails('page', 'source');
	$thumbnail_size = $this->post_type_repo->thumbnails('page', 'display_size');
	
	if ( $thumbnail_source ) :		
		if ( has_post_thumbnail($this->post->nav_object_id) && $this->post->nav_type != 'taxonomy' ) :
			$out = '<div class="np-thumbnail ' . esc_attr($thumbnail_size) . '">';
			$image = get_the_post_thumbnail($this->post->nav_object_id, $thumbnail_source);
			$out .= apply_filters('nestedpages_thumbnail', $image, $this->post);
		else :
			$out = '<div class="np-thumbnail link">';
			$fallback_icon = 'np-icon-link';
			if ( $this->post->nav_type == 'taxonomy' ) $fallback_icon = 'np-icon-tag';
			if ( $this->post->nav_object == 'post' ) $fallback_icon = 'np-icon-post';
			if ( $this->post->nav_object == 'page' ) $fallback_icon = 'np-icon-page';
			$image_fallback = '<i class="' . esc_attr($fallback_icon) . '" /></i>';
			$image_fallback = apply_filters('nestedpages_thumbnail_fallback', $image_fallback, $this->post);
			if ( $image_fallback ) :
				$out .= $image_fallback;
			endif;
		endif;
		$out .= '</div>';
		echo $out;
	endif;
	?>

	<div class="np-bulk-checkbox">
		<input type="checkbox" name="nestedpages_bulk[]" value="<?php echo esc_attr($this->post->id); ?>" data-np-bulk-checkbox="<?php echo esc_attr($this->post->title); ?>" class="np-redirect-bulk" data-np-post-type="<?php echo esc_attr($this->post->post_type); ?>" />
	</div>
</div><!-- .row -->