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

		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-sub-menu"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 15l-6 6-1.42-1.42L15.17 16H4V4h2v10h9.17l-3.59-3.58L13 9l6 6z" class="arrow" /></svg>
		
		<?php if ( $this->user->canSortPages() && !$this->listing_repo->isSearch() ) : ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
		<?php endif; ?>

		<a href="<?php echo $link; ?>" class="page-link page-title" target="_blank">
			<span class="title">
				<?php echo apply_filters('the_title', $this->post->title, $this->post->id, $view = 'nestedpages_title'); ?> 
				<svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="icon" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
			</span>
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
				<?php _e('Quick Edit', 'wp-nested-pages'); ?>
			</a>

			<?php if ( current_user_can('delete_pages') ) : ?>
			<a href="<?php echo get_delete_post_link($this->post->id, '', true); ?>" class="np-btn np-btn-trash" data-np-confirm-delete>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-remove"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg>
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