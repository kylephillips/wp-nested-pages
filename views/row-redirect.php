<?php
/**
* Redirect Page
*/
?>
<div class="row">
	<div class="child-toggle"></div>
	<div class="row-inner">
		<i class="np-icon-sub-menu"></i>
		<?php if ( current_user_can('edit_theme_options') ) : ?>
		<i class="handle np-icon-menu"></i>
		<?php endif; ?>
		<a href="<?php echo NP_Helpers::check_url(get_the_content()); ?>" class="page-link page-title" target="_blank">
			<span class="title"><?php the_title(); ?> <i class="np-icon-link"></i></span>
			<?php 

				// Post Status
				if ( $post->post_status !== 'publish' ){
					echo '<span class="status">(' . $post->post_status . ')</span>';
				} else {
					echo '<span class="status"></span>';
				}

				// Nested Pages Status
				if ( $this->post_data['np_status'] == 'hide' )
					echo '<i class="np-icon-eye-blocked"></i>';

				// Nav Status
				if ( $this->post_data['nav_status'] == 'hide' ){
					echo '<span class="nav-status">(Hidden)</span>';
				} else {
					echo '<span class="nav-status"></span>';
				}
			?>
		</a>

		<a href="#" class="np-toggle-edit"><i class="np-icon-pencil"></i></a>

		<div class="action-buttons">

			<a href="#" 
				class="np-btn np-quick-edit-redirect" 
				data-id="<?php echo get_the_id(); ?>" 
				data-parentid="<?php echo $this->post_data['parent_id']; ?>"
				data-title="<?php the_title(); ?>" 
				data-url="<?php echo NP_Helpers::check_url(get_the_content()); ?>"
				data-status="<?php echo get_post_status(); ?>" 
				data-np-status="<?php echo $this->post_data['np_status']; ?>"
				data-navstatus="<?php echo $this->post_data['nav_status']; ?>"
				data-navtitleattr="<?php echo $this->post_data['nav_title_attr']; ?>"
				data-navcss="<?php echo $this->post_data['nav_css']; ?>"
				data-linktarget="<?php echo $this->post_data['link_target']; ?>">
				<?php _e('Quick Edit', 'nestedpages'); ?>
			</a>

			<?php if ( current_user_can('delete_pages') ) : ?>
			<a href="<?php echo get_delete_post_link(get_the_id(), '', true); ?>" class="np-btn np-btn-trash">
				<i class="np-icon-remove"></i>
			</a>
			<?php endif; ?>

		</div><!-- .action-buttons -->
	</div><!-- .row-inner -->
</div><!-- .row -->