<?php
/**
* Redirect Page
*/
$link = ( $this->post->nav_type && $this->post->nav_type !== 'custom' ) 
	? $this->post->nav_original_link
	: esc_url($this->post->content);
$original_id = esc_attr($this->post->nav_object_id);
$new_window = true;
if ( $original_id && $original_id !== '' ) :
	$display_edit = ( !current_user_can('edit_others_posts') && $this->post->author !== get_current_user_id() ) ? false : true;
	if ( $display_edit ) {
		$link = get_edit_post_link($original_id);
		$new_window = false;
	}
endif;
?>
<div class="row <?php if ( $this->listing_repo->isSearch() || $this->listing_repo->isOrdered($this->post_type->name) ) echo 'search';?> <?php echo apply_filters('nestedpages_link_row_css_classes', $row_classes, $this->post, $this->post_type); ?>">
	
	<?php if ( $this->post_type->hierarchical ) : ?>
	<div class="child-toggle">
		<div class="child-toggle-spacer"></div>
	</div>
	<?php else : ?>
	<div class="non-hierarchical-spacer"></div>
	<?php endif; ?>

	<div class="row-inner">

		<img src="<?php echo \NestedPages\Helpers::plugin_url() . '/assets/images/arrow-child.svg'; ?>" alt="<?php _e('Arrow', 'wp-nested-pages'); ?>" class="np-icon-sub-menu">
		
		<?php if ( $this->user->canSortPosts($this->post_type->name) && !$this->listing_repo->isSearch() && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>
		<img src="<?php echo \NestedPages\Helpers::plugin_url() . '/assets/images/handle.svg'; ?>" alt="<?php _e('Sorting Handle', 'wp-nested-pages'); ?>" class="handle np-icon-menu">
		<?php endif; ?>

		<a href="<?php echo $link; ?>" class="page-link page-title" <?php if ( $new_window ) echo 'target="_blank"'; ?>>
			<span class="title">
				<?php echo apply_filters('the_title', $this->post->title, $this->post->id); ?> 
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/link.svg" alt="<?php _e('Link Icon', 'wp-nested-pages'); ?>" class="link-icon">
			</span>
			<?php 

				// Post Status
				echo ( $this->post->status !== 'publish' )
					? '<span class="status">(' . __(ucfirst($this->post->status)) . ')</span>'
					: '<span class="status"></span>';

				// Nested Pages Status
				if ( $this->post->np_status == 'hide' )
					echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/hidden.svg" alt="' . __('Hidden Icon', 'wp-nested-pages') . '" class="row-status-icon status-np-hidden">';

				// Nav Status
				echo ( $this->post->nav_status == 'hide' )
					? '<span class="nav-status">(' . __('Hidden', 'wp-nested-pages') . ')</span>'
					: '<span class="nav-status"></span>';
			?>
		</a>

		<div class="action-buttons">

			<?php if ( in_array('quickedit', $this->post_type_settings->row_actions) ) : ?>
			<a href="#" 
				class="np-btn np-quick-edit-redirect" 
				data-id="<?php echo esc_attr($this->post->id); ?>" 
				data-parentid="<?php echo esc_attr($this->post->parent_id); ?>"
				data-title="<?php echo esc_attr($this->post->title); ?>" 
				data-url="<?php echo esc_url($this->post->content); ?>"
				data-status="<?php echo esc_attr($this->post->status); ?>" 
				data-np-status="<?php echo esc_attr($this->post->np_status); ?>"
				data-navstatus="<?php echo esc_attr($this->post->nav_status); ?>"
				data-navtitleattr="<?php echo esc_attr($this->post->nav_title_attr); ?>"
				data-navcss="<?php echo esc_attr($this->post->nav_css); ?>"
				data-nav-type="<?php echo esc_attr($this->post->nav_type); ?>"
				data-nav-object="<?php echo esc_attr($this->post->nav_object); ?>"
				data-nav-object-id="<?php echo esc_attr($this->post->nav_object_id); ?>"
				data-nav-original-link="<?php echo esc_attr($this->post->nav_original_link); ?>"
				data-nav-original-title="<?php echo esc_attr(strip_tags(html_entity_decode($this->post->nav_original_title))); ?>"
				data-linktarget="<?php echo esc_attr($this->post->link_target); ?>">
				<?php _e('Quick Edit', 'wp-nested-pages'); ?>
			</a>
			<?php endif; ?>

			<?php if ( current_user_can('delete_pages') && in_array('trash', $this->post_type_settings->row_actions) ) : ?>
			<a href="<?php echo get_delete_post_link($this->post->id, '', true); ?><?php if ( $this->post_type->name !== 'page' ) echo '&parent_post_type=' . $this->post_type->name; ?>" class="np-btn np-btn-trash" data-np-confirm-delete>
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/trash.svg" alt="<?php _e('Trash Icon', 'wp-nested-pages'); ?>" class="np-icon-remove">
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
			$out = '<div class="np-thumbnail ' . esc_attr($thumbnail_size) . '">';
			$image_fallback = apply_filters('nestedpages_thumbnail_fallback', false, $this->post);
			if ( $image_fallback ) :
				$out .= $image_fallback;
			endif;
		endif;
		$out .= '</div>';
		echo $out;
	endif;

	if ( $this->can_user_perform_bulk_actions ) : ?>
	<div class="np-bulk-checkbox">
		<input type="checkbox" name="nestedpages_bulk[]" value="<?php echo esc_attr($this->post->id); ?>" data-np-bulk-checkbox="<?php echo esc_attr($this->post->title); ?>" class="np-redirect-bulk" data-np-post-type="<?php echo esc_attr($this->post->post_type); ?>" />
	</div>
	<?php endif ?>
</div><!-- .row -->
