<?php
/**
* Row represents a single page
*/
$wpml = $this->integrations->plugins->wpml->installed;
$wpml_pages = ( $wpml && $this->integrations->plugins->wpml->isDefaultLanguage()) ? true : false;
if ( !$wpml ) $wpml_pages = true;
?>
<div class="row<?php echo $row_classes; ?>">
	
	<?php if ( $this->post_type->hierarchical ) : ?>
	<div class="child-toggle">
		<div class="child-toggle-spacer"></div>
	</div>
	<?php endif; ?>

	<?php if ( !$this->post_type->hierarchical ) echo '<div class="non-hierarchical-spacer"></div>'; ?>

	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		
		<?php if ( $this->user->canSortPages() && !$this->listing_repo->isSearch() && !$this->post_type_settings->disable_sorting && $wpml_current_language !== 'all' ) : ?>
		<i class="handle np-icon-menu"></i>
		<?php endif; ?>

		<a href="<?php echo apply_filters('nestedpages_edit_link', get_edit_post_link(), $this->post); ?>" class="page-link page-title">
			<span class="title">
				<?php 
					echo apply_filters( 'the_title', $this->post->title, $this->post->id, $view = 'nestedpages_title' ); 
					if ( !$assigned_pt ) :
						if ( $this->post->id == get_option('page_on_front') ) echo ' <em class="np-page-type"><strong>&ndash; ' . __('Front Page', 'wp-nested-pages') . '</strong></em>';
						if ( $this->post->id == get_option('page_for_posts') ) echo ' <em class="np-page-type"><strong>&ndash; ' . __('Posts Page', 'wp-nested-pages') . '</strong></em>';
					endif;
				?>
			</span>
			<?php 
				// Post Status
				echo '<span class="status">';
				if ( $this->post->status !== 'publish' )	echo '(' . __(ucfirst($this->post->status)) . ')';
				if ( post_password_required($this->post->id) ) echo ' <i class="np-icon-lock"></i>';
				echo '</span>';

				// Nested Pages Status
				if ( $this->post->np_status == 'hide' && !$wpml )
					echo '<i class="np-icon-eye-blocked"></i>';

				// Nav Status
				if ( $this->post->nav_status == 'hide' && !$wpml ){
					echo '<span class="nav-status">' . __('Hidden', 'wp-nested-pages') . '</span>';
				} else {
					echo '<span class="nav-status"></span>';
				}
				
				// Post Lock
				if ( $user = wp_check_post_lock($this->post->id) ){
					$u = get_userdata($user);
					echo '<span class="locked"><i class="np-icon-lock"></i><em> ' . esc_html($u->display_name) . ' ' . __('currently editing', 'wp-nested-pages') . '</em></span>';
				} elseif ( !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ){
					echo '<span class="locked"><i class="np-icon-lock"></i></span>';
				} else {
					echo '<span class="edit-indicator"><i class="np-icon-pencil"></i>' . apply_filters('nestedpages_edit_link_text', __('Edit'), $this->post) . '</span>';
				}

				// Sticky
				echo '<span class="sticky"';
				if ( !in_array($this->post->id, $this->sticky_posts) ) echo ' style="display:none;"';
				echo '>(' . __('Sticky', 'wp-nested-pages') . ')<span>';

				if ( $this->post->status !== 'publish' )	echo '(' . __(ucfirst($this->post->status)) . ')';
				if ( post_password_required($this->post->id) ) echo ' <i class="np-icon-lock"></i>';
				
				
				
			?>
		</a>

		<?php if ( $assigned_pt ) : ?>
		<ul class="np-assigned-pt-actions">
			<?php if ( current_user_can('publish_posts') ) : ?>
			<li><a href="<?php echo $this->post_type_repo->addNewPostLink($assigned_pt->name); ?>" class=""><?php echo $assigned_pt->labels->add_new; ?></a></li>
			<?php endif; ?>
			<li><a href="<?php echo $this->post_type_repo->allPostsLink($assigned_pt->name); ?>" class=""><?php echo $assigned_pt->labels->all_items . ' (' . $this->listing_repo->postCount($assigned_pt->name) . ')'; ?></a></li>
		</ul>
		<?php endif; ?>

		<!-- Responsive Toggle Button -->
		<a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a>

		<?php if ( !$this->post->hierarchical ) : echo $this->post->hierarchical; ?>
		<div class="np-post-columns">
			<ul class="np-post-info">
				<li><span class="np-author-display"><?php echo $this->post->author; ?></span></li>
				<li><?php echo get_the_date(); ?></li>
			</ul>
		</div>
		<?php endif; ?>

		<?php
		if ( $this->integrations->plugins->yoast->installed ){
			echo '<span class="np-seo-indicator ' . esc_html($this->post->score) . '"></span>';
		}
		?>

		<div class="action-buttons">
			<?php if ( $wpml ) : ?>
				<a href="#" class="np-btn" data-nestedpages-translations><?php _e('Translations', 'wp-nested-pages'); ?> (<?php echo $this->integrations->plugins->wpml->getAllTranslations($this->post->id, 'count'); ?>)</a>
			<?php endif; ?>

			<?php if ( $this->post->comment_status == 'open' ) : $comments = wp_count_comments($this->post->id); $cs = 'open' ?>
			
			<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>" class="np-btn">
				<i class="np-icon-bubble"></i> <?php echo $comments->total_comments; ?>
			</a>
			
			<?php else : $cs = 'closed'; endif; ?>

			<?php if ( current_user_can('publish_pages') && $this->post_type->hierarchical && !$this->listing_repo->isSearch() && $wpml_pages ) : ?>
		
			<?php if (!$this->settings->menusDisabled() && !$this->integrations->plugins->wpml->installed) : ?>
			<a href="#" class="np-btn open-redirect-modal" data-parentid="<?php echo esc_attr($this->post->id); ?>"><i class="np-icon-link"></i></a>
			<?php endif; ?>
			
			<a href="#" class="np-btn add-new-child" data-id="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php echo esc_html($this->post->title); ?>"><?php _e('Add Child', 'wp-nested-pages'); ?></a>

			<?php endif; ?>

			<?php if ( current_user_can('edit_pages') && current_user_can('edit_posts') && $wpml_pages ) : ?>
			<a href="#" class="np-btn clone-post" data-id="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php echo esc_html($this->post->title); ?>"><?php _e('Clone', 'wp-nested-pages'); ?></a>
			<?php endif; ?>

			<?php if ( !$user = wp_check_post_lock($this->post->id) || !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ) : ?>
			<a href="#" 
				class="np-btn np-quick-edit" 
				data-id="<?php echo esc_attr($this->post->id); ?>" 
				data-template="<?php echo esc_attr($this->post->template); ?>" 
				data-title="<?php echo esc_attr($this->post->title); ?>" 
				data-slug="<?php echo esc_attr(urldecode($post->post_name)); ?>" 
				data-commentstatus="<?php echo esc_attr($cs); ?>" 
				data-status="<?php echo esc_attr($this->post->status); ?>" 
				data-np-status="<?php echo esc_attr($this->post->np_status); ?>"
				data-navstatus="<?php echo esc_attr($this->post->nav_status); ?>" 
				data-navtitleattr="<?php echo esc_attr($this->post->nav_title_attr); ?>"
				data-navcss="<?php echo esc_attr($this->post->nav_css); ?>"
				data-linktarget="<?php echo esc_attr($this->post->link_target); ?>" 
				data-navtitle="<?php echo esc_attr($this->post->nav_title); ?>"
				data-author="<?php echo esc_attr($post->post_author); ?>" 
				<?php if ( current_user_can('publish_pages') ) : ?>
				data-password="<?php echo esc_attr($post->post_password); ?>"
				<?php endif; ?>
				data-month="<?php echo esc_attr($this->post->date->month); ?>" 
				data-day="<?php echo esc_attr($this->post->date->d); ?>" 
				data-year="<?php echo esc_attr($this->post->date->y); ?>" 
				data-hour="<?php echo esc_attr($this->post->date->h); ?>" 
				data-minute="<?php echo esc_attr($this->post->date->m);?>"
				data-datepicker="<?php echo date_i18n('n/j/Y', $this->post->date->datepicker); ?>"
				data-time="<?php echo date_i18n('H:i', $this->post->date->datepicker); ?>"
				data-formattedtime="<?php echo date_i18n('g:i', $this->post->date->datepicker); ?>"
				data-timeformat="<?php echo get_option('time_format'); ?>"
				data-ampm="<?php echo date('a', $this->post->date->datepicker); ?>"
				data-sticky="<?php if ( in_array($this->post->id, $this->sticky_posts) ) echo 'sticky'; ?>">
				<?php _e('Quick Edit'); ?>
			</a>
			<?php endif; ?>

			<a href="<?php echo apply_filters('nestedpages_view_link', get_the_permalink(), $this->post); ?>" class="np-btn np-view-button" target="_blank">
				<?php echo apply_filters('nestedpages_view_link_text', __('View'), $this->post); ?>
			</a>
			
			<!-- <a href="#" class="np-btn"><i class="np-icon-more_vert"></i></a> -->
			
			<?php if ( current_user_can('delete_pages') && $this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ) : ?>
			<a href="<?php echo get_delete_post_link(get_the_id()); ?>" class="np-btn np-btn-trash">
				<i class="np-icon-remove"></i>
			</a>
			<?php endif; ?>

		</div><!-- .action-buttons -->
	</div><!-- .row-inner -->

	<?php
	// Thumbnail
	$thumbnail_source = $this->post_type_repo->thumbnails($this->post_type->name, 'source');
	$thumbnail_size = $this->post_type_repo->thumbnails($this->post_type->name, 'display_size');
	if ( $thumbnail_source ) :
		$out = '<div class="np-thumbnail ' . $thumbnail_size . '">';
		if ( has_post_thumbnail($this->post->id) ) :
			$image = get_the_post_thumbnail($this->post->id, $thumbnail_source);
			$out .= apply_filters('nestedpages_thumbnail', $image, $this->post);
		else :
			$image_fallback = apply_filters('nestedpages_thumbnail_fallback', false, $this->post);
			if ( $image_fallback ) :
				$out .= apply_filters('nestedpages_thumbnail_fallback', $image_fallback, $this->post);
			endif;
		endif;
		$out .= '</div>';
		echo $out;
	endif;
	?>

	<div class="np-bulk-checkbox">
		<input type="checkbox" name="nestedpages_bulk[]" value="<?php echo esc_attr($this->post->id); ?>" data-np-bulk-checkbox="<?php echo esc_attr($this->post->title); ?>" data-np-post-type="<?php echo esc_attr($this->post->post_type); ?>" />
	</div>
</div><!-- .row -->