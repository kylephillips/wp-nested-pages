<?php
/**
* Modal Form for adding a new link
*/
$post_type_object = get_post_type_object( 'page' );
$can_publish = current_user_can( $post_type_object->cap->publish_posts );
?>
<div class="np-modal fade" id="np-link-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div id="npmenuitems" class="np-tabbed-content" data-np-tab-pane>
				<form data-np-menu-item-form action="">
				<div class="modal-body np-menu-item-form">
					<div class="np-menu-type-selection">
						<ul data-np-menu-accordion>

							<li><a href="#" class="np-custom-link" data-np-menu-object="custom" data-np-menu-type="custom" data-np-menu-objectid="" data-np-permalink="" data-np-menu-selection><?php _e('Custom Link', 'nestedpages'); ?></a></li>

							<?php
								// Post Types
								foreach ( $this->listing_repo->postTypes() as $name => $type ) {
									$recent_posts = $this->listing_repo->recentPosts($name);
									if ( !$recent_posts ) continue;
									$out = '<li><a href="#" data-np-menu-accordion-item>' . $type->labels->name . '</a>';
									$out .= '<ul>';
									$out .= '<li class="np-menu-search"><input type="text" data-np-menu-search data-search-type="post_type" data-search-object="' . $name . '" placeholder="' . __('Search', 'nestedpages') . ' ' . $type->labels->name . '" /><div class="np-menu-search-loading"></div><div class="np-menu-search-noresults">' . __('No Results', 'nestedpages') . '</div></li>';
									foreach ( $recent_posts as $post ){
										$out .= '<li data-default-result><a href="#" data-np-menu-object="' . $name . '" data-np-menu-type="post_type" data-np-menu-objectid="' . $post->ID . '" data-np-permalink="' . get_the_permalink($post->ID) . '" data-np-object-name="' . $type->labels->singular_name . '" data-np-menu-selection>' . $post->post_title . '</a></li>';
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
									$out = '<li><a href="#" data-np-menu-accordion-item>' . $taxonomy->labels->name . '</a>';
									$out .= '<ul>';
									$out .= '<li class="np-menu-search"><input type="text" data-np-menu-search data-search-type="taxonomy" data-search-object="' . $name . '" placeholder="' . __('Search', 'nestedpages') . ' ' . $taxonomy->labels->name . '" /><div class="np-menu-search-loading"></div><div class="np-menu-search-noresults">' . __('No Results', 'nestedpages') . '</div></li>';
									foreach ( $terms as $term ){
										$out .= '<li data-default-result><a href="#" data-np-menu-object="' . $name . '" data-np-menu-type="taxonomy" data-np-menu-objectid="' . $term->term_id . '" data-np-permalink="' . get_term_link($term) . '" data-np-object-name="' . $taxonomy->labels->name . '" data-np-menu-selection>' . $term->name . '</a></li>';
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
							<?php _e('Select an item', 'nestedpages'); ?>
						</div>
						<div class="np-menu-link-details" style="display:none;">
							<h3><span data-np-menu-title></span> <em></em></h3>
							<div class="original-link">
								<?php _e('Original', 'nestedpages'); ?>: <span data-np-original-link></span>
							</div>
							<div class="np-quickedit-error" data-np-error style="clear:both;display:none;"></div>
							<div class="fields">
								<p data-np-menu-url-cont style="display:none;">
									<label><?php _e('URL', 'nestedpages'); ?></label>
									<input type="text" name="url" data-np-menu-url />
								</p>
								<p>
									<label><?php _e('Navigation Label', 'nestedpages'); ?></label>
									<input type="text" name="navigationLabel" data-np-menu-navigation-label />
								</p>
								<p>
									<label><?php _e('Title Attribute', 'nestedpages'); ?></label>
									<input type="text" name="titleAttribute" data-np-menu-title-attr />
								</p>
								<p>
									<label><?php _e('CSS Classes (optional)', 'nestedpages'); ?></label>
									<input type="text" name="cssClasses" data-np-menu-css-classes />
								</p>
								<?php if ( $this->user->canSortPages() ) : // Menu Options Button ?>
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
					<input type="hidden" name="menuTitle" data-np-menu-title value="">
					<input type="hidden" name="objectType" data-np-menu-object-input value="">
					<input type="hidden" name="objectId" data-np-menu-objectid-input value="">
					<input type="hidden" name="menuType" data-np-menu-type-input value="">
					<input type="hidden" name="parent_id" class="parent_id" value="">
					<button type="button" class="button modal-close" data-dismiss="modal">
						<?php _e('Cancel'); ?>
					</button>

					<a accesskey="s" class="button-primary" data-np-save-link style="display:none;float:right;">
						<?php _e( 'Add', 'nestedpages' ); ?>
					</a>
					<span class="np-qe-loading"></span>

				</div><!-- .modal-footer -->
				</form>
			</div><!-- #npmenuitems -->
			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->