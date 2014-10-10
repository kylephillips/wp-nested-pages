<div class="row">
	<div class="child-toggle"></div>
	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		<i class="handle np-icon-menu"></i>
		<a href="<?php echo get_edit_post_link(); ?>" class="page-link">
			<?php the_title(); ?> <span><i class="np-icon-pencil"></i>Edit</span>
		</a>
		<div class="action-buttons">
			<?php if ( comments_open() ) : $comments = wp_count_comments(get_the_id()); ?>
			<a href="#" class="np-btn"><i class="np-icon-bubble"></i> <?php echo $comments->total_comments; ?></a>
			<?php endif; ?>
			<a href="#" class="np-btn">Add Child</a>
			<a href="#" class="np-btn np-quick-edit">Quick Edit</a>
			<a href="<?php echo get_the_permalink(); ?>" class="np-btn">View</a>
		</div>
	</div><!-- .row-inner -->
</div><!-- .row -->
