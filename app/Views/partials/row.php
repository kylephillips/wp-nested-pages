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
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-sub-menu"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 15l-6 6-1.42-1.42L15.17 16H4V4h2v10h9.17l-3.59-3.58L13 9l6 6z" class="arrow" /></svg>
		
		<?php 
		$sortable = apply_filters('nestedpages_post_sortable', true, $this->post, $this->post_type);
		if ( $this->user->canSortPosts($this->post_type->name) && !$this->listing_repo->isSearch() && !$this->post_type_settings->disable_sorting && $wpml_current_language !== 'all' && !$this->listing_repo->isOrdered($this->post_type->name) && $sortable ) : ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="handle np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
		<?php endif; ?>

		<a href="<?php echo apply_filters('nestedpages_edit_link', get_edit_post_link(), $this->post); ?>" class="page-link page-title">
			<span class="title">
				<?php 
					$title = apply_filters( 'the_title', $this->post->title, $this->post->id ); 
					echo apply_filters('nestedpages_post_title', $title, $this->post);
					echo $this->postStates($assigned_pt);
				?>
			</span>
			<?php 
				// Post Status
				echo '<span class="status">';
				if ( $this->post->status !== 'publish' ) : 
					global $wp_post_statuses;
					echo '(' . $wp_post_statuses[$this->post->status]->label . ')';
				endif;
				if ( post_password_required($this->post->id) ) {
					echo '<span class="locked password-required">';
					echo ' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>';
					echo '</span>';
				}
				echo '</span>';

				// Nested Pages Status
				if ( $this->post->np_status == 'hide' && !$wpml )
					echo '<svg class="row-status-icon status-np-hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path class="icon" d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>';

				// Nav Status
				echo ( $this->post->nav_status == 'hide' && !$wpml )
					? '<span class="nav-status">' . __('Hidden', 'wp-nested-pages') . '</span>'
					: '<span class="nav-status"></span>';
				
				// Post Lock
				if ( $user = wp_check_post_lock($this->post->id) ){
					$u = get_userdata($user);
					echo '<span class="locked">';
					echo ' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>';
					echo '<em> ' . sprintf(__('%s currently editing', 'wp-nested-pages'), esc_html($u->display_name)) . '</em></span>';
				} elseif ( !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ){
					echo '<span class="locked">';
					echo ' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>';
					echo '</span>';
				} else {
					$display_edit = ( !current_user_can('edit_others_posts') && $this->post->author !== get_current_user_id() ) ? false : true;
					if ( $display_edit ) echo '<span class="edit-indicator">' . apply_filters('nestedpages_edit_link_text', __('Edit', 'wp-nested-pages'), $this->post) . '</span>';
				}

				// Sticky
				echo '<span class="sticky"';
				$sticky_text = apply_filters('nestedpages_make_sticky_text_row', __('(Sticky)', 'wp-nested-pages'), $this->post, $this->post_type);
				if ( !in_array($this->post->id, $this->sticky_posts) ) echo ' style="display:none;"';
				echo '>' . $sticky_text . '<span>';

				if ( post_password_required($this->post->id) ) {
					echo ' <span class="status-icon-locked"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg></span>';
				}
			?>
		</a>

		<?php echo $this->rowActions($assigned_pt); ?>

		<?php if ( !$this->post->hierarchical ) : echo $this->post->hierarchical; ?>
		<div class="np-post-columns">
			<ul class="np-post-info">
				<li><span class="np-author-display"><?php echo apply_filters('nestedpages_post_author', $this->post->author, $this->post); ?></span></li>
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
			
			<div class="nestedpages-dropdown" data-dropdown>
				<a href="#" class="np-btn has-icon toggle" data-dropdown-toggle>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
						<path d="M0 0h24v24H0z" fill="none"/>
						<path class="circles" d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
					</svg>
				</a>
				<ul class="nestedpages-dropdown-content" data-dropdown-content>
					
					<?php 
					// WPML Translations
					if ( $wpml && in_array('wpml', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" data-nestedpages-translations>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
						<?php echo sprintf(__('Translations (%s)', 'wp-nested-pages'), $this->integrations->plugins->wpml->getAllTranslations($this->post->id, 'count')); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php 
					// Comments
					if ( $this->post->comment_status == 'open' && in_array('comments', $this->post_type_settings->row_actions) ) : $comments = wp_count_comments($this->post->id); $cs = 'open' ?>
					<li>
						<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="primary" d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
						<?php echo sprintf(__('%d Comments', 'wp-nested-pages'), intval($comments->total_comments)); ?>
						</a>
					</li>
					<?php else : $cs = 'closed'; endif; ?>

					<?php 
					$include_link_dropdown = ( $this->post_type->name == 'page' ) ? true : false;
					$include_link_dropdown = apply_filters('nestedpages_include_links_dropdown', $include_link_dropdown, $this->post_type);

					if ( (current_user_can('publish_pages') || $this->user->canSubmitPending($this->post_type->name)) && $this->post_type->hierarchical && !$this->listing_repo->isSearch() && $wpml_pages ) :  

					// Link
					if ( !$this->settings->menusDisabled() && !$this->integrations->plugins->wpml->installed && in_array('add_child_link', $this->post_type_settings->row_actions) && $include_link_dropdown ) : ?>
					<li>
						<a href="#" class="open-redirect-modal" data-parentid="<?php esc_attr_e($this->post->id); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="primary" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
						<?php _e('Add Child Link', 'wp-nested-pages'); ?></a>
					</li>
					<?php endif; ?>
			
					<?php if ( in_array('add_child_page', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" class="add-new-child" data-id="<?php esc_attr_e(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="primary" d="M3 21h18v-2H3v2zM3 8v8l4-4-4-4zm8 9h10v-2H11v2zM3 3v2h18V3H3zm8 6h10V7H11v2zm0 4h10v-2H11v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
						<?php echo sprintf(__('Add Child %s', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
					</li>
					<?php endif; ?>

					<?php endif; ?>

					<?php if ( (current_user_can('publish_pages') || $this->user->canSubmitPending($this->post_type->name) ) && !$this->listing_repo->isSearch() && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>

					<?php if ( in_array('insert_before', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" data-insert-before="<?php esc_attr_e(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="primary" d="M8 11h3v10h2V11h3l-4-4-4 4zM4 3v2h16V3H4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
						<?php printf(esc_html__('Insert %s Before', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
					</li>
					<?php endif; ?>

					<?php if ( in_array('insert_after', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" data-insert-after="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path class="primary" d="M16 13h-3V3h-2v10H8l4 4 4-4zM4 19v2h16v-2H4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
						<?php printf(esc_html__('Insert %s After', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
					</li>
					<?php endif; ?>

					<?php endif; ?>

					<?php if ( $this->user->canSortPosts($this->post_type->name) && !$this->listing_repo->isSearch() && !$this->post_type_settings->disable_sorting && $wpml_current_language !== 'all' && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>

					<?php if ( in_array('push_to_top', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" data-push-to-top>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path fill="none" d="M0 0h24v24H0V0z"/>
							<path class="primary" d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"/>
						</svg>
						<?php _e('Push to Top', 'wp-nested-pages'); ?></a>
					</li>
					<?php endif; ?>

					<?php if ( in_array('push_to_bottom', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" data-push-to-bottom>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path fill="none" d="M0 0h24v24H0V0z"/>
							<path fill="#010101" class="primary" d="M20 12l-1.41-1.41L13 16.17V4h-2v12.17l-5.58-5.59L4 12l8 8 8-8z"/>
						</svg>
						<?php _e('Push to Bottom', 'wp-nested-pages'); ?></a>
					</li>
					<?php endif; ?>

					<?php endif; ?>

					<?php if ( current_user_can('edit_pages') && current_user_can('edit_posts') && $wpml_pages && in_array('clone', $this->post_type_settings->row_actions) ) : ?>
					<li>
						<a href="#" class="clone-post" data-id="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path class="primary" d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4l6 6v10c0 1.1-.9 2-2 2H7.99C6.89 23 6 22.1 6 21l.01-14c0-1.1.89-2 1.99-2h7zm-1 7h5.5L14 6.5V12z"/></svg>
						<?php _e('Clone', 'wp-nested-pages'); ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div><!-- .dropdown -->

			<?php 
			$can_quickedit_post = apply_filters('nestedpages_quickedit', true, $this->post);
			if ( !current_user_can('edit_others_posts') ){
				$author = get_post_field('post_author', $this->post->ID);
				if ( intval($author) !== get_current_user_id() ) $can_quickedit_post = false;
			}
			if ( !$user = wp_check_post_lock($this->post->id) || !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) && current_user_can('edit_posts', $this->post) && $can_quickedit_post && in_array('quickedit', $this->post_type_settings->row_actions) ) : 
			if ( $can_quickedit_post ) :
			?>
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
				data-sticky="<?php if ( in_array($this->post->id, $this->sticky_posts) ) echo 'sticky'; ?>"
				<?php echo $this->custom_fields_repo->dataAttributes($this->post, $this->post_type); ?>
				>
				<?php _e('Quick Edit', 'wp-nested-pages'); ?>
			</a>
			<?php endif; endif; ?>

			<?php
			/**
			* View/Preview Link
			*/
			if ( in_array('view', $this->post_type_settings->row_actions) ) : 
			if ( $this->post->status == 'publish' ) : 
			$link = apply_filters('nestedpages_view_link', get_the_permalink(), $this->post);
			$link = ( $this->post_type->name !== 'post' ) 
				? apply_filters('page_link', $link, $this->post->ID, false) 
				: apply_filters('post_link', $link, $this->post, false);
			?>
			<a href="<?php echo $link; ?>" class="np-btn np-view-button" target="_blank">
				<?php echo apply_filters('nestedpages_view_link_text', __('View', 'wp-nested-pages'), $this->post); ?>
			</a>
			<?php 
			else :
			$link = apply_filters('nestedpages_preview_link', get_the_permalink(), $this->post);
			$link = apply_filters('preview_post_link', $link, $this->post);
			?>
			<a href="<?php echo $link; ?>" class="np-btn np-view-button" target="_blank">
				<?php echo apply_filters('nestedpages_preview_link_text', __('Preview', 'wp-nested-pages'), $this->post); ?>
			</a>
			<?php
			endif; // status
			endif; // View in row actions
			?>
			
			<?php if ( current_user_can('delete_pages') && $this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id)  && in_array('trash', $this->post_type_settings->row_actions) ) : ?>
			<?php if ( $this->post_type->hierarchical && $this->publishedChildrenCount($this->post) > 0 ) : ?>
			<div class="nestedpages-dropdown" data-dropdown>
				<a href="#" class="np-btn np-btn-trash" data-dropdown-toggle>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-remove"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg>
				</a>
				<ul class="nestedpages-dropdown-content" data-dropdown-content>
					<li>
						<a href="<?php echo get_delete_post_link(get_the_id()); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="np-icon-remove"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg>
						<?php printf(__('Trash %s', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
					</li>
					<li>
						<a href="#" data-nestedpages-trash-children data-post-id="<?php echo $this->post->ID; ?>"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve"><path d="M19,4h-3.5l-1-1h-5l-1,1H5v2h14V4z M6,7v12c0,1.1,0.9,2,2,2h8c1.1,0,2-0.9,2-2V7H6z M10,14v-4h4v4h2l-4,4l-4-4H10z"/><path style="fill:none;" d="M0,0h24v24H0V0z"/></svg><?php printf(__('Trash %s & Children', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
					</li>
				</ul>
			</div>
			<?php else : ?>
				<a href="<?php echo get_delete_post_link(get_the_id()); ?>" class="np-btn np-btn-trash"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="icon"/><path d="M0 0h24v24H0z" fill="none"/></svg></a>
			<?php endif; ?>
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
