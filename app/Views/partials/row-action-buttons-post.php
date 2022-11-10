<div class="action-buttons">
	<div class="nestedpages-dropdown" data-dropdown>
		<a href="#" class="np-btn has-icon toggle" data-dropdown-toggle>
			<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/more.svg" alt="<?php _e('More Icon', 'wp-nested-pages'); ?>">
		</a>
		<ul class="nestedpages-dropdown-content" data-dropdown-content>
			<?php 
			// WPML Translations
			if ( $wpml && in_array('wpml', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" data-nestedpages-translations>
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/globe.svg" alt="<?php _e('Globe Icon', 'wp-nested-pages'); ?>">
				<?php echo sprintf(__('Translations (%s)', 'wp-nested-pages'), $this->integrations->plugins->wpml->getAllTranslations($this->post->id, 'count')); ?>
				</a>
			</li>
			<?php endif; ?>

			<?php 
			// Comments
			if ( $this->post->comment_status == 'open' && in_array('comments', $this->post_type_settings->row_actions) ) : $comments = wp_count_comments($this->post->id); $cs = 'open' ?>
			<li>
				<a href="<?php echo admin_url( 'edit-comments.php?p=' . get_the_id() ); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/comments.svg" alt="<?php _e('Comments Icon', 'wp-nested-pages'); ?>">
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
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/link.svg" alt="<?php _e('Link Icon', 'wp-nested-pages'); ?>">
				<?php _e('Add Child Link', 'wp-nested-pages'); ?></a>
			</li>
			<?php endif; ?>
	
			<?php if ( in_array('add_child_page', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" class="add-new-child" data-id="<?php esc_attr_e(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/child-page.svg" alt="<?php _e('Child Page Icon', 'wp-nested-pages'); ?>">
				<?php echo sprintf(__('Add Child %s', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			</li>
			<?php endif; ?>

			<?php endif; ?>

			<?php if ( (current_user_can('publish_pages') || $this->user->canSubmitPending($this->post_type->name) ) && !$this->listing_repo->isSearch() && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>

			<?php if ( in_array('insert_before', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" data-insert-before="<?php esc_attr_e(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/insert-before.svg" alt="<?php _e('Insert Before Icon', 'wp-nested-pages'); ?>">
				<?php printf(esc_html__('Insert %s Before', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( in_array('insert_after', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" data-insert-after="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/insert-after.svg" alt="<?php _e('Insert After Icon', 'wp-nested-pages'); ?>">
				<?php printf(esc_html__('Insert %s After', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			</li>
			<?php endif; ?>

			<?php endif; ?>

			<?php if ( $this->user->canSortPosts($this->post_type->name) && !$this->listing_repo->isSearch() && !$this->post_type_settings->disable_sorting && $wpml_current_language !== 'all' && !$this->listing_repo->isOrdered($this->post_type->name) ) : ?>

			<?php if ( in_array('push_to_top', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" data-push-to-top>
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/arrow-up.svg" alt="<?php _e('Arrow Up Icon', 'wp-nested-pages'); ?>">
				<?php _e('Push to Top', 'wp-nested-pages'); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( in_array('push_to_bottom', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" data-push-to-bottom>
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/arrow-down.svg" alt="<?php _e('Arrow Down Icon', 'wp-nested-pages'); ?>">
				<?php _e('Push to Bottom', 'wp-nested-pages'); ?></a>
			</li>
			<?php endif; ?>

			<?php endif; ?>

			<?php if ( current_user_can('edit_pages') && current_user_can('edit_posts') && in_array('clone', $this->post_type_settings->row_actions) ) : ?>
			<li>
				<a href="#" class="clone-post" data-id="<?php echo esc_attr(get_the_id()); ?>" data-parentname="<?php esc_html_e($this->post->title); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/clone.svg" alt="<?php _e('Clone Icon', 'wp-nested-pages'); ?>">
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
		data-custom-url="<?php echo esc_attr($this->post->nav_custom_url); ?>"
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
			<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/trash.svg" alt="<?php _e('Trash Icon', 'wp-nested-pages'); ?>" class="np-icon-remove">
		</a>
		<ul class="nestedpages-dropdown-content" data-dropdown-content>
			<li>
				<a href="<?php echo get_delete_post_link(get_the_id()); ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/trash-black.svg" alt="<?php _e('Trash Icon', 'wp-nested-pages'); ?>" class="np-icon-remove">
				<?php printf(__('Trash %s', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			</li>
			<li>
				<a href="#" data-nestedpages-trash-children data-post-id="<?php echo $this->post->ID; ?>">
				<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/trash-children.svg" alt="<?php _e('Trash Children Icon', 'wp-nested-pages'); ?>" class="np-icon-remove">
				<?php printf(__('Trash %s & Children', 'wp-nested-pages'), $this->post_type->labels->singular_name); ?></a>
			</li>
		</ul>
	</div>
	<?php else : ?>
		<a href="<?php echo get_delete_post_link(get_the_id()); ?>" class="np-btn np-btn-trash">
			<img src="<?php echo \NestedPages\Helpers::plugin_url(); ?>/assets/images/trash.svg" alt="<?php _e('Trash Icon', 'wp-nested-pages'); ?>" class="np-icon-remove">
		</a>
	<?php endif; ?>
	<?php endif; ?>
</div><!-- .action-buttons -->