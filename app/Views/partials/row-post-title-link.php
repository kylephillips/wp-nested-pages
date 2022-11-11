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
			echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/lock.svg" alt="' . __('Lock Icon', 'wp-nested-pages') . '">';
			echo '</span>';
		}
		echo '</span>';

		// Nested Pages Status
		if ( $this->post->np_status == 'hide' && !$wpml )
			echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/hidden.svg" alt="' . __('Hidden Icon', 'wp-nested-pages') . '" class="row-status-icon status-np-hidden">';

		// Nav Status
		echo ( $this->post->nav_status == 'hide' && !$wpml )
			? '<span class="nav-status">' . __('Hidden', 'wp-nested-pages') . '</span>'
			: '<span class="nav-status"></span>';
		
		// Post Lock
		if ( $user = wp_check_post_lock($this->post->id) ){
			$u = get_userdata($user);
			echo '<span class="locked">';
			echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/lock.svg" alt="' . __('Lock Icon', 'wp-nested-pages') . '">';
			echo '<em> ' . sprintf(__('%s currently editing', 'wp-nested-pages'), esc_html($u->display_name)) . '</em></span>';
		} elseif ( !$this->integrations->plugins->editorial_access_manager->hasAccess($this->post->id) ){
			echo '<span class="locked">';
			echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/lock.svg" alt="' . __('Lock Icon', 'wp-nested-pages') . '">';
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
			echo '<img src="' . \NestedPages\Helpers::plugin_url() . '/assets/images/lock.svg" alt="' . __('Lock Icon', 'wp-nested-pages') . '">';
		}
	?>
</a>