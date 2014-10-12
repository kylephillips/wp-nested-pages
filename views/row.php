<div class="row">
	<div class="child-toggle"></div>
	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		<i class="handle np-icon-menu"></i>
		<a href="<?php echo get_edit_post_link(); ?>" class="page-link page-title">
			<span class="title"><?php the_title(); ?></span>
			<?php 
				// Post Status
				if ( $post->post_status !== 'publish' ){
					echo '<span class="status">(' . $post->post_status . ')</span>';
				} else {
					echo '<span class="status"></span>';
				}

				// Nav Status
				if ( $nav_status == 'hide' ){
					echo '<span class="nav-status">(Hidden)</span>';
				} else {
					echo '<span class="nav-status"></span>';
				}
				
				// Post Lock
				if ( $user = wp_check_post_lock(get_the_id()) ){
					$u = get_userdata($user);
					echo '<span class="locked"><i class="np-icon-lock"></i><em> ' . $u->display_name . ' ' . __('currently editing', 'nestedpages') . '</em></span>';
				} else {
					echo '<span class="edit-indicator"><i class="np-icon-pencil"></i>' . __('Edit', 'nestedpages') . '</span>';
				}
			?>
		</a>

		<a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a>

		<div class="action-buttons">
			<?php if ( $post->comment_status == 'open' ) : $comments = wp_count_comments(get_the_id()); $cs = 'open' ?>
			
			<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>" class="np-btn">
				<i class="np-icon-bubble"></i> <?php echo $comments->total_comments; ?>
			</a>
			
			<?php else : $cs = 'closed'; ?>
			
			<?php endif; ?>
			
			<a href="#" class="np-btn"><?php _e('Add Child', 'nestedpages'); ?></a>

			<?php if ( !$user = wp_check_post_lock(get_the_id()) ) : ?>
			<a href="#" 
				class="np-btn np-quick-edit" 
				data-id="<?php echo get_the_id(); ?>" 
				data-template="<?php echo $template; ?>" 
				data-title="<?php the_title(); ?>" 
				data-slug="<?php echo $post->post_name; ?>" 
				data-commentstatus="<?php echo $cs; ?>" 
				data-status="<?php echo get_post_status(); ?>" 
				data-navstatus="<?php echo $nav_status; ?>"
				data-author="<?php echo $post->post_author; ?>" 
				data-month="<?php echo $month; ?>" 
				data-day="<?php echo $d; ?>" 
				data-year="<?php echo $y; ?>" 
				data-hour="<?php echo $h; ?>" 
				data-minute="<?php echo $m; ?>">
				<?php _e('Quick Edit', 'nestedpages'); ?>
			</a>
			<?php endif; ?>

			<a href="<?php echo get_the_permalink(); ?>" class="np-btn"><?php _e('View', 'nestedpages'); ?></a>
		</div><!-- .action-buttons -->
	</div><!-- .row-inner -->
</div><!-- .row -->
