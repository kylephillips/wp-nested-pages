<div class="row">
	<div class="child-toggle"></div>
	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		<i class="handle np-icon-menu"></i>
		<a href="<?php echo get_edit_post_link($page->ID); ?>" class="page-link">
			<?php echo $page->post_title; ?> <span><i class="np-icon-pencil"></i>Edit</span>
		</a>
		<div class="action-buttons">
			<?php if ( $page->comment_status == 'open' ) : ?>
			<a href="#" class="np-btn"><i class="np-icon-bubble"></i> <?php echo $page->comment_count; ?></a>
			<?php endif; ?>
			<a href="#" class="np-btn">Add Child</a>
			<a href="#" class="np-btn">Quick Edit</a>
			<a href="<?php echo get_the_permalink($page->ID); ?>" class="np-btn">View</a>
		</div>
	</div><!-- .row-inner -->
</div><!-- .row -->
