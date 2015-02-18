<?php
/**
* Row represents a single page
*/
?>
<div class="row<?php if ( !$this->post_type->hierarchical ) echo ' non-hierarchical'; ?>" <?php if ( $this->isSearch() ) echo 'style="padding-left:10px;"';?>>
	
	<?php if ( $this->post_type->hierarchical && !$this->isSearch() ) : ?>
	<div class="child-toggle"></div>
	<?php endif; ?>

	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		
		<?php if ( $this->user->canSortPages() && !$this->isSearch() ) : ?>
		<i class="handle np-icon-menu"></i>
		<?php endif; ?>

		<a href="<?php echo get_edit_post_link(); ?>" class="page-link page-title">
			<span class="title"><?php echo $this->post->title; ?></span>
			<?php 
				
				if ( function_exists('wpseo_auto_load') ){
					echo '<span class="np-seo-indicator ' . $this->post->score . '"></span>';
				}

				// Post Status
				echo '<span class="status">';
				if ( $this->post->status !== 'publish' )	echo '(' . __(ucfirst($this->post->status)) . ')';
				if ( post_password_required($this->post->id) ) echo ' <i class="np-icon-lock"></i>';
				echo '</span>';

				// Nested Pages Status
				if ( $this->post->np_status == 'hide' )
					echo '<i class="np-icon-eye-blocked"></i>';

				// Nav Status
				if ( $this->post->nav_status == 'hide' ){
					echo '<span class="nav-status">' . __('Hidden', 'nestedpages') . '</span>';
				} else {
					echo '<span class="nav-status"></span>';
				}
				
				// Post Lock
				if ( $user = wp_check_post_lock($this->post->id) ){
					$u = get_userdata($user);
					echo '<span class="locked"><i class="np-icon-lock"></i><em> ' . $u->display_name . ' ' . __('currently editing', 'nestedpages') . '</em></span>';
				} elseif ( !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ){
					echo '<span class="locked"><i class="np-icon-lock"></i></span>';
				} else {
					echo '<span class="edit-indicator"><i class="np-icon-pencil"></i>' . __('Edit') . '</span>';
				}
				
			?>
		</a>


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


		<div class="action-buttons">

			<?php if ( $this->post->comment_status == 'open' ) : $comments = wp_count_comments($this->post->id); $cs = 'open' ?>

			
			<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>" class="np-btn">
				<i class="np-icon-bubble"></i> <?php echo $comments->total_comments; ?>
			</a>
			
			<?php else : $cs = 'closed'; endif; ?>


			<?php if ( current_user_can('publish_pages') && $this->post_type->hierarchical && !$this->isSearch() ) : ?>
		
			<a href="#" class="np-btn open-redirect-modal" data-parentid="<?php echo $this->post->id; ?>"><i class="np-icon-link"></i></a>
			
			<a href="#" class="np-btn add-new-child" data-id="<?php echo get_the_id(); ?>" data-parentname="<?php echo $this->post->title; ?>"><?php _e('Add Child', 'nestedpages'); ?></a>

			<?php endif; ?>

			<?php if ( !$user = wp_check_post_lock($this->post->id) || !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ) : ?>
			<a href="#" 
				class="np-btn np-quick-edit" 
				data-id="<?php echo $this->post->id; ?>" 
				data-template="<?php echo $this->post->template; ?>" 
				data-title="<?php echo $this->post->title; ?>" 
				data-slug="<?php echo $post->post_name; ?>" 
				data-commentstatus="<?php echo $cs; ?>" 
				data-status="<?php echo $this->post->status; ?>" 
				data-np-status="<?php echo $this->post->np_status; ?>"
				data-navstatus="<?php echo $this->post->nav_status; ?>" 
				data-navtitleattr="<?php echo $this->post->nav_title_attr; ?>"
				data-navcss="<?php echo $this->post->nav_css; ?>"
				data-linktarget="<?php echo $this->post->link_target; ?>" 
				data-navtitle="<?php echo $this->post->nav_title; ?>"
				data-author="<?php echo $post->post_author; ?>" 
				<?php if ( current_user_can('publish_pages') ) : ?>
				data-password="<?php echo $post->post_password; ?>"
				<?php endif; ?>
				data-month="<?php echo $this->post->date->month; ?>" 
				data-day="<?php echo $this->post->date->d; ?>" 
				data-year="<?php echo $this->post->date->y; ?>" 
				data-hour="<?php echo $this->post->date->h; ?>" 
				data-minute="<?php echo $this->post->date->m;?>"
				data-datepicker="<?php echo date_i18n('n/j/Y', $this->post->date->datepicker); ?>"
				data-time="<?php echo date_i18n('H:i', $this->post->date->datepicker); ?>"
				data-formattedtime="<?php echo date_i18n('g:i', $this->post->date->datepicker); ?>"
				data-ampm="<?php echo date('a', $this->post->date->datepicker); ?>">
				<?php _e('Quick Edit'); ?>
			</a>
			<?php endif; ?>

			<a href="<?php echo get_the_permalink(); ?>" class="np-btn" target="_blank"><?php _e('View'); ?></a>
			
			<?php if ( current_user_can('delete_pages') && $this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ) : ?>
			<a href="<?php echo get_delete_post_link(get_the_id()); ?>" class="np-btn np-btn-trash">
				<i class="np-icon-remove"></i>
			</a>
			<?php endif; ?>

		</div><!-- .action-buttons -->
	</div><!-- .row-inner -->
</div><!-- .row -->