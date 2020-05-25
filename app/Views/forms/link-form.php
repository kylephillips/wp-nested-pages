<?php
/**
* Modal Form for adding a new link
*/
$post_type_object = get_post_type_object( 'page' );
$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>
<div class="nestedpages-modal-backdrop" data-nestedpages-modal="np-link-modal"></div>
<div class="nestedpages-modal-content np-link-modal-content <?php if ( $this->integrations->plugins->dark_mode->installed ) echo 'np-dark-mode'; ?>" id="np-link-modal" data-nestedpages-modal="np-link-modal">

	<div class="modal-content">
		
		<div id="npmenuitems" class="np-tabbed-content" data-np-tab-pane>
			<form data-np-menu-item-form action="">
			<div class="modal-body np-menu-item-form">
				<div class="np-menu-type-selection">
					<ul data-np-menu-accordion>

						<li><a href="#" class="np-custom-link" data-np-menu-object="custom" data-np-menu-type="custom" data-np-menu-objectid="" data-np-permalink="" data-np-menu-selection><?php _e('Custom Link', 'wp-nested-pages'); ?></a></li>

						<?php
							// Post Types
							foreach ( $this->listing_repo->postTypes() as $name => $type ) {
								$recent_posts = $this->listing_repo->recentPosts($name);
								if ( !$recent_posts ) continue;
								$out = '<li><a href="#" data-np-menu-accordion-item>' . esc_html($type->labels->name) . '</a>';
								$out .= '<ul>';
								$out .= '<li class="np-menu-search"><input type="text" data-np-menu-search data-search-type="post_type" data-search-object="' . esc_attr($name) . '" placeholder="' . __('Search', 'wp-nested-pages') . ' ' . esc_attr($type->labels->name) . '" />';
								$out .= '<div class="np-default-loading np-menu-search-loading">';
								ob_start();
									include( NestedPages\Helpers::asset('images/spinner.svg') );
									$out .= ob_get_contents();
								ob_end_clean();
								$out .= '</div>';
								$out .= '<div class="np-menu-search-noresults">' . __('No Results', 'wp-nested-pages') . '</div></li>';

								if ( $type->has_archive ) :
									$out .= '<li data-default-result class="post-type-archive"><a href="#" data-np-menu-object="' . esc_attr($name) . '" data-np-menu-type="post_type_archive" data-np-object-name="' . sprintf(__('%s (Archive)'), esc_attr($type->labels->name)) . '" data-np-permalink="' . get_post_type_archive_link($name) . '" data-np-menu-selection>' . sprintf(__('%s (Archive)', 'wp-nested-pages'), esc_html($type->labels->name)) . '</a></li>';
								endif;

								foreach ( $recent_posts as $post ){
									$out .= '<li data-default-result><a href="#" data-np-menu-object="' . esc_attr($name) . '" data-np-menu-type="post_type" data-np-menu-objectid="' . esc_attr($post->ID) . '" data-np-permalink="' . get_the_permalink($post->ID) . '" data-np-object-name="' . esc_attr($type->labels->singular_name) . '" data-np-menu-selection>' . esc_html($post->post_title) . '</a></li>';
								}
								$out .= '</ul>';
								$out .= '</li>';
								echo $out;
							}
						?>
						
						<?php 
							// Taxonomies
							foreach ( $this->listing_repo->taxonomies() as $name => $taxonomy ) {
								$terms = $this->listing_repo->terms($name);
								if ( !$terms ) continue;
								$out = '<li><a href="#" data-np-menu-accordion-item>' . esc_html($taxonomy->labels->name) . '</a>';
								$out .= '<ul>';
								$out .= '<li class="np-menu-search"><input type="text" data-np-menu-search data-search-type="taxonomy" data-search-object="' . esc_attr($name) . '" placeholder="' . __('Search', 'wp-nested-pages') . ' ' . esc_attr($taxonomy->labels->name) . '" /><div class="np-menu-search-loading"></div><div class="np-menu-search-noresults">' . __('No Results', 'wp-nested-pages') . '</div></li>';
								foreach ( $terms as $term ){
									$out .= '<li data-default-result><a href="#" data-np-menu-object="' . esc_attr($name) . '" data-np-menu-type="taxonomy" data-np-menu-objectid="' . esc_attr($term->term_id) . '" data-np-permalink="' . esc_attr(get_term_link($term)) . '" data-np-object-name="' . esc_attr($taxonomy->labels->name) . '" data-np-menu-selection>' . esc_html($term->name) . '</a></li>';
								}
								$out .= '</ul>';
								$out .= '</li>';
								echo $out;
							}
						?>
						
					</ul>
				</div><!-- .np-menu-type-selection -->
				<div class="np-menu-link-object">
					<div class="np-menu-link-object-placeholder">
						<?php _e('Select an item', 'wp-nested-pages'); ?>
					</div>
					<div class="np-menu-link-details" style="display:none;">
						<h3><span data-np-menu-title></span> <em></em></h3>
						<div class="original-link">
							<?php _e('Original', 'wp-nested-pages'); ?>: <span data-np-original-link></span>
						</div>
						<div class="np-quickedit-error" data-np-error style="clear:both;display:none;"></div>
						<div class="fields">
							<p data-np-menu-url-cont style="display:none;">
								<label><?php _e('URL', 'wp-nested-pages'); ?></label>
								<input type="text" name="url" data-np-menu-url />
							</p>
							<p>
								<label><?php _e('Navigation Label', 'wp-nested-pages'); ?></label>
								<input type="text" name="navigationLabel" data-np-menu-navigation-label />
							</p>
							<p>
								<label><?php _e('Title Attribute', 'wp-nested-pages'); ?></label>
								<input type="text" name="titleAttribute" data-np-menu-title-attr />
							</p>
							<p>
								<label><?php _e('CSS Classes (optional)', 'wp-nested-pages'); ?></label>
								<input type="text" name="cssClasses" data-np-menu-css-classes />
							</p>
							<?php if ( $this->user->canSortPosts($this->post_type->name) ) : // Menu Options Button ?>
							<label class="checkbox">
								<input type="checkbox" name="linkTarget" class="link_target" data-np-menu-link-target />
								<span class="checkbox-title"><?php _e( 'Open link in a new window/tab' ); ?></span>
							</label>
							<?php endif; ?>
						</div><!-- .fields -->
					</div><!-- .np-menu-link-details -->
				</div>
			</div><!-- .modal-body -->

			<div class="modal-footer">
				<div class="footer-inner">
					<input type="hidden" name="menuTitle" data-np-menu-title value="">
					<input type="hidden" name="objectType" data-np-menu-object-input value="">
					<input type="hidden" name="objectId" data-np-menu-objectid-input value="">
					<input type="hidden" name="menuType" data-np-menu-type-input value="">
					<input type="hidden" name="parent_id" class="parent_id" value="">
					<input type="hidden" name="parent_post_type" class="parent-post-type" value="<?php echo $this->post_type->name; ?>" data-np-menu-parent-post-type>
					<button type="button" class="button modal-close" data-nestedpages-modal-close>
						<?php _e('Cancel', 'wp-nested-pages'); ?>
					</button>

					<a accesskey="s" class="button-primary" data-np-save-link style="display:none;float:right;">
						<?php _e( 'Add', 'wp-nested-pages' ); ?>
					</a>
					<div class="np-qe-loading">
						<?php include( NestedPages\Helpers::asset('images/spinner.svg') ); ?>
					</div>
				</div><!-- .footer-inner -->
			</div><!-- .modal-footer -->
			
			</form>
		</div><!-- #npmenuitems -->
		
</div><!-- /.modal -->