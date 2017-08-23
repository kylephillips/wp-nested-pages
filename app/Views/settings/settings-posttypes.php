<?php
$types = $this->getPostTypes();
$thumbnail_sizes = get_intermediate_image_sizes();
settings_fields( 'nestedpages-posttypes' ); 
?>

<h3><?php _e('Enable Post Types:', 'wp-nested-pages'); ?></h3>

<div class="nestedpages-settings-table">
	<?php foreach ($types as $type) : ?>
	<div class="row-container">
		<div class="head">
			<div class="checkbox">
				<input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>]" value="<?php echo esc_attr($type->name); ?>" <?php if ( $type->np_enabled ) echo 'checked'; ?> data-nestedpages-settings-row-checkbox id="post-type-<?php echo esc_attr($type->name); ?>" />
			</div>
			<label for="post-type-<?php echo $type->name; ?>">
				<?php 
					echo esc_html($type->label); 
					if ( $type->hierarchical ) echo ' <em>(' . __('Hierarchical', 'wp-nested-pages') . ')</em>';
				?>
			</label>
			<a href="#" class="button" data-toggle-nestedpages-pt-settings><?php _e('Settings', 'wp-nested-pages'); ?></a>
		</div><!-- .head -->
		<div class="body">
			<ul class="settings-details">
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Replace Default Menu', 'wp-nested-pages'); ?>*</strong><br />
							<?php _e('Replace the default top-level item with the nested view link.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][replace_menu]" value="true" <?php if ( $type->replace_menu ) echo 'checked'; ?> /><?php echo __('Replace Default') . ' ' . esc_html($type->label) . ' ' . __('Menu', 'wp-nested-pages'); ?></label>
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Remove Default Link', 'wp-nested-pages'); ?></strong><br />
							<?php _e('If the default menu is replaced, a link to the default view will be added. Select this to remove the link', 'wp-nested-pages'); ?>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][hide_default]" value="true" <?php if ( $type->hide_default ) echo 'checked'; ?> /><?php echo __('Hide Default', 'wp-nested-pages') . ' ' . esc_html($type->label) . ' ' . __('Link', 'wp-nested-pages'); ?></label>
						</div>
					</div><!-- .row -->
				</li>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Disable Sorting', 'wp-nested-pages'); ?></strong><br />
							<?php _e('Remove drag and drop sorting from this post type.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][disable_sorting]" value="true" <?php if ( $type->disable_sorting ) echo 'checked'; ?> /><?php echo __('Disable Sorting') ?></label>
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<?php if ( $type->hierarchical ) : ?>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Disable Nesting', 'wp-nested-pages'); ?>**</strong><br>
							<?php _e('To disable nesting on hierarchical post types, select this option.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][disable_nesting]" value="true" <?php if ( $type->disable_nesting ) echo 'checked '; ?>/><?php echo __('Disable Nesting for', 'wp-nested-pages') . ' ' . esc_html($type->label); ?></label>
						</div>
					</div><!-- .row -->
				</li>
				<?php endif; ?>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Assign Page for Listing', 'wp-nested-pages'); ?></strong><br />
							<?php _e('Adds contextual links and post counts to the page row for the assigned post type.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][post_type_page_assignment]" <?php if ( $type->page_assignment && $this->post_repo->postExists($type->page_assignment_id, 'page') ) echo 'checked'; ?> data-nestedpages-assign-post-type value="true" /><?php _e('Assign Page', 'wp-nested-pages'); ?></label>
							<input type="hidden" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][post_type_page_assignment_page_id]" data-nested-pages-assign-post-type-id value="<?php if ( $type->page_assignment_id ) echo $type->page_assignment_id; ?>" />
							<input type="hidden" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][post_type_page_assignment_page_title]" data-nested-pages-assign-post-type-title value="<?php if ( $type->page_assignment_title ) echo $type->page_assignment_title; ?>" />
							
							<div class="nestedpages-assignment-display" style="display:none;">
								<div class="nestedpages-page-pt-assignment-selection" data-nestedpages-page-pt-assignment-selection <?php if ( !$type->page_assignment_id || !$this->post_repo->postExists($type->page_assignment_id, 'page') ) echo 'style="display:none;"'?>>
									<?php if ( $type->page_assignment_id ) : ?>
									<?php _e('Currently assigned to:', 'wp-nested-pages'); ?> <?php echo $type->page_assignment_title; ?> <a href="#" data-nestedpages-page-pt-assignment-remove>(<?php _e('Remove', 'wp-nested-pages'); ?>)</a>
									<?php endif; ?>
								</div>
								<div class="nestedpages-page-pt-assignment" data-nestedpages-post-search-form <?php if ( $type->page_assignment_id && $this->post_repo->postExists($type->page_assignment_id, 'page') ) echo 'style="display:none"';?>>
									<?php
									$recent_pages = $this->listing_repo->recentPosts('page');
									if ( $recent_pages ) :
									?>
									<input type="search" data-nestedpages-post-search="page" placeholder="<?php _e('Search Pages', 'wp-nested-pages'); ?>" />
									<div class="np-quickedit-info" style="display:none;" data-nestedpages-no-results><?php _e('No pages were found.', 'wp-nested-pages'); ?></div>
									<img src="<?php echo NestedPages\Helpers::plugin_url(); ?>/assets/images/spinner.gif" alt="<?php _e('Loading', 'wp-nested-pages'); ?>" style="display:none;" data-nestedpages-loading />
									<div class="nestedpages-page-search-results" data-nestedpages-search-results>
										<ul>
										<?php foreach($recent_pages as $page) : ?>
											<li><a href="#" data-assignment-page-id="<?php echo esc_attr($page->ID); ?>" data-assignment-page-title="<?php echo esc_html($page->post_title); ?>"><?php echo esc_html($page->post_title); ?></a></li>
										<?php endforeach; ?>
										</ul>
									</div><!-- .nestedpages-page-search-results -->
									<?php else : ?>
										<div class="np-quickedit-info">
											<?php _e('There are currently no pages available.', 'wp-nested-pages'); ?>
										</div>
									<?php endif; ?>
								</div><!-- .nestedpages-page-pt-assignment -->
							</div><!-- .nestedpages-assignment-display -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<li>
					<?php 
						$thumbnails_enabled = $this->post_type_repo->thumbnails($type->name, 'enabled'); 
						$thumbnail_source = $this->post_type_repo->thumbnails($type->name, 'source'); 
						$thumbnail_size = $this->post_type_repo->thumbnails($type->name, 'display_size'); 
					?>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Display Thumbnails', 'wp-nested-pages'); ?></strong><br>
							<?php _e('Display the thumbnail in the list sort view.', 'wp-nested-pages'); ?><br><br>
							<?php _e('Note: Thumbnail width is displayed at a maximum of 80px in the nested pages view. The image is scaled proportionally.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][thumbnails][display]" value="true" <?php if ( $thumbnails_enabled ) echo 'checked'; ?> data-enable-thumbnails /><?php echo __('Display Thumbnails for', 'wp-nested-pages') . ' ' . esc_html($type->label); ?></label>
							
							<div class="thumbnail-options" data-thumbnail-options <?php if ( !$thumbnails_enabled ) echo 'style="display:none;"'; ?>>
								<label><?php _e('Thumbnail Source', 'wp-nested-pages'); ?></label>
								<select name="nestedpages_posttypes[<?php echo $type->name; ?>][thumbnails][size]">
								<?php
								foreach ( $thumbnail_sizes as $size ){
									echo '<option value="' . esc_attr($size) . '"';
									if ( $size == $thumbnail_source ) echo ' selected';
									echo '>' . esc_html($size) . '</option>';
								}
								?>
								</select>

								<label><?php _e('Thumbnail Display Size', 'wp-nested-pages'); ?></label>
								<select name="nestedpages_posttypes[<?php echo $type->name; ?>][thumbnails][display_size]">
									<option value="small" <?php if ( $thumbnail_size == 'small' ) echo ' selected';?>><?php _e('Small', 'wp-nested-pages'); ?>(50px)</option>
									<option value="medium" <?php if ( $thumbnail_size == 'medium' ) echo ' selected';?>><?php _e('Medium', 'wp-nested-pages'); ?>(80px)</option>
									<option value="large" <?php if ( $thumbnail_size == 'large' ) echo ' selected';?>><?php _e('Large', 'wp-nested-pages'); ?>(150px)</option>
								</select>
							</div><!-- .thumbnail-options -->
						</div>
					</div><!-- .row -->
				</li>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Configure Standard Fields', 'wp-nested-pages'); ?></strong><br>
							<?php _e('Remove standard fields from the quick edit form.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" data-toggle-nestedpages-sf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][standard_fields_enabled]" value="true" <?php if ( $type->standard_fields_enabled ) echo 'checked '; ?>/><?php _e('Configure Standard Fields', 'wp-nested-pages'); ?></label>

							<div class="standard-fields">
								<h5><?php _e('Check to remove from Quick Edit.', 'wp-nested-pages'); ?></h5>
								<div class="custom-field-group">
								<ul>
									<?php
										$out = "";
										foreach ( $this->settings->standardFields($type->name) as $name => $label ) :
											if ( $name != 'taxonomies' ) :
												$out .= '<li>';
												$out .= '<label>';
												$out .= '<input type="checkbox" name="nestedpages_posttypes[' . esc_attr($type->name) . '][standard_fields][standard][' . esc_attr($name) . ']" value="true"';
												if ( $name == 'hide_taxonomies' ) $out .= ' data-hide-taxonomies';
												if ( $this->post_type_repo->fieldEnabled($type->name, 'standard', $name, 'standard_fields') ) $out .= ' checked';
												$out .= ' />' . esc_html($label);
												$out .= '</label>';
												$out .= '</li>';
											else : // Taxonomies
												foreach ( $label as $tax_name => $tax_label ) :
													$disabled = $this->post_type_repo->taxonomyDisabled($tax_name, $type->name);
													$out .= '<li data-taxonomy-single style="margin-left:20px;';
													if ( $this->post_type_repo->fieldEnabled($type->name, 'standard', 'hide_taxonomies', 'standard_fields') ) $out .= 'display:none;';
													$out .= '">';
													$out .= '<label>';
													$out .= '<input type="checkbox" name="nestedpages_posttypes[' . esc_attr($type->name) . '][standard_fields][standard][taxonomies][' . esc_attr($tax_name) . ']" value="true"';
													if ( $disabled ) $out .= ' checked';
													$out .= ' />' . esc_html($tax_label);
													$out .= '</label>';
													$out .= '</li>';
												endforeach;
											endif;
										endforeach;
										echo $out;
									?>
								</ul>
								</div><!-- .custom-field-group -->
							</div><!-- .standard-fields -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<?php if ( $this->integrations->plugins->acf->installed ) : ?>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Configure Custom Fields', 'wp-nested-pages'); ?></strong><br>
							<?php _e('Set which custom fields display in the quick edit form.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" data-toggle-nestedpages-cf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][custom_fields_enabled]" value="true" <?php if ( $type->custom_fields_enabled ) echo 'checked'; ?> /><?php _e('Configure Custom Fields', 'wp-nested-pages'); ?></label>

							<div class="custom-fields">
							<h5><?php _e('Check to Include in Quick Edit.', 'wp-nested-pages'); ?></h5>
							<?php
								// Advanced Custom Fields
								$acf_fields = $this->integrations->plugins->acf->getFieldsForPostType($type->name);
								if ( $acf_fields ) :
									$out = '<div class="custom-field-group">';
									$out .= '<p>' . __('Advanced Custom Fields', 'wp-nested-pages') . '</p>';
									$out .= '<ul class="indented">';
									foreach ($acf_fields as $field){
										$out .= '<li>';
										$out .= '<label>';
										$out .= '<input type="checkbox" name="nestedpages_posttypes[' . esc_attr($type)->name . '][custom_fields][acf][' . esc_attr($field['key']) . ']" value="' . esc_attr($field['type']) . '"'; 
										if ( $this->post_type_repo->fieldEnabled($type->name, 'acf', $field['key']) ) $out .= ' checked';
										$out .= '/>' . esc_html($field['label']) . ' (' . esc_html($field['type']) . ')';
										$out .= '</label>';
										$out .= '</li>';
									}
									$out .= '</ul>';
									$out .= '</div><!-- .custom-field-group -->';
									echo $out;
								else : 
									echo __('No ACF Fields configured for this post type', 'wp-nested-pages');
								endif;
								?>
							</div><!-- .custom-fields -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
			<?php endif; ?>
			<?php 
			if ( $type->name !== 'page' ) : 
			$h_taxonomies = $this->post_type_repo->getTaxonomies($type->name, true); 
			$f_taxonomies = $this->post_type_repo->getTaxonomies($type->name, false);
			$taxonomies = array_merge($h_taxonomies, $f_taxonomies);
			?>
			<li>
				<div class="row">
					<div class="description">
						<p><strong><?php _e('Sort Options', 'wp-nested-pages'); ?></strong><br />
						<?php _e('Add and remove sort options.', 'wp-nested-pages'); ?></p>
					</div>
					<div class="field">
						<div class="nestedpages-sort-options-selection">
							<label>
								<input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][sort_options][author]" value="true" <?php if ( $this->post_type_repo->sortOptionEnabled($type->name, 'author') ) echo 'checked'; ?> />
								<?php _e('Author') ?>
							</label>
							<label>
								<input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][sort_options][orderby]" value="true" <?php if ( $this->post_type_repo->sortOptionEnabled($type->name, 'orderby') ) echo 'checked'; ?> />
								<?php _e('Order By') ?>
							</label>
							<label>
								<input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][sort_options][order]" value="true" <?php if ( $this->post_type_repo->sortOptionEnabled($type->name, 'order') ) echo 'checked'; ?> />
								<?php _e('Order') ?>
							</label>

							<?php if ( !empty($taxonomies) ) : foreach ( $taxonomies as $tax ) : ?>
							<label>
								<input type="checkbox" name="nestedpages_posttypes[<?php echo esc_attr($type->name); ?>][sort_options][taxonomies][<?php echo $tax->name; ?>]" value="true" <?php if ( $this->post_type_repo->sortOptionEnabled($type->name, $tax->name, true) ) echo 'checked'; ?> />
								<?php echo $tax->label; ?>
							</label>
							<?php endforeach; endif; ?>
							
						<!-- .nestedpages-sort-options-selection -->
					</div><!-- .field -->
				</div><!-- .row -->
			</li>
			<?php endif; ?>
			</ul>
		</div><!-- .body -->
	</div><!-- .post-type -->
	<?php endforeach; ?>
</div><!-- .nestedpages-settings-posttypes -->

<div class="nestedpages-settings-disclaimers">
	<p style="font-size:12px;margin-bottom:15px;">
		*<?php _e('If default menu is not replaced, an additional submenu item will be added for "Nested/Sort View"', 'wp-nested-pages'); ?>
	</p>
	<p style="font-size:12px;">
		**<?php _e('<strong>Important:</strong> Changing page structures on live sites may effect SEO and existing inbound links. Limit URL structure changes on live sites by disabling nesting. Sorting within the current nesting structure will still be available. If nesting changes are made to a live site, it may help to add a 301 redirect from the old location to the new one.', 'wp-nested-pages'); ?>
	</p>
</div>