<div class="row">
	<div class="child-toggle"></div>
	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		<i class="handle np-icon-menu"></i>
		<a href="<?php echo get_edit_post_link(); ?>" class="page-link page-title">
			<?php the_title(); ?> <span><i class="np-icon-pencil"></i>Edit</span>
		</a>
		<div class="action-buttons">
			<?php if ( $post->comment_status == 'open' ) : $comments = wp_count_comments(get_the_id()); $cs = 'open' ?>
			<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>" class="np-btn"><i class="np-icon-bubble"></i> <?php echo $comments->total_comments; ?></a>
			<?php else : $cs = 'closed'; ?>
			<?php endif; ?>
			<a href="#" class="np-btn">Add Child</a>

			<a href="#" class="np-btn np-quick-edit" data-template="<?php echo $template; ?>" data-title="<?php the_title(); ?>" data-slug="<?php echo $post->post_name; ?>" data-commentstatus="<?php echo $cs; ?>" data-status="<?php echo get_post_status(); ?>" data-author="<?php echo $post->post_author; ?>" data-month="<?php echo $month; ?>" data-day="<?php echo $d; ?>" data-year="<?php echo $y; ?>" data-hour="<?php echo $h; ?>" data-minute="<?php echo $m; ?>">Quick Edit</a>

			<a href="<?php echo get_the_permalink(); ?>" class="np-btn">View</a>
		</div>
	</div><!-- .row-inner -->
</div><!-- .row -->
