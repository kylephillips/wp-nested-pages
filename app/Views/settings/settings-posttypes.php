<?php
$types = $this->getPostTypes();
$thumbnail_sizes = get_intermediate_image_sizes();
settings_fields( 'nestedpages-posttypes' );
?>

<h3><?php _e('Enable Nested Pages for:', 'wp-nested-pages'); ?></h3>

<div class="nestedpages-settings-posttypes">
	<?php foreach ($types as $type) : ?>
	<div class="post-type">
		<div class="head">
			<div class="checkbox">
				<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>]" value="<?php echo $type->name; ?>" <?php if ( $type->np_enabled ) echo 'checked'; ?> data-nestedpages-pt-checkbox id="post-type-<?php echo $type->name; ?>" />
			</div>
			<label for="post-type-<?php echo $type->name; ?>">
				<?php 
					echo $type->label; 
					if ( $type->hierarchical ) echo ' <em>(' . __('Hierarchical', 'wp-nested-pages') . ')</em>';
				?>
			</label>
			<a href="#" class="button" data-toggle-nestedpages-pt-settings><?php _e('Settings', 'wp-nested-pages'); ?></a>
		</div><!-- .head -->
		<div class="body">
			<ul class="post-type-settings">
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Replace Default Menu', 'wp-nested-pages'); ?>*</strong><br />
							<?php _e('Replace the default top-level item with the nested view link.', 'wp-nested-pages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][replace_menu]" value="true" <?php if ( $type->replace_menu ) echo 'checked'; ?> /><?php echo __('Replace Default') . ' ' . $type->label . ' ' . __('Menu', 'wp-nested-pages'); ?></label>
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
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][hide_default]" value="true" <?php if ( $type->hide_default ) echo 'checked'; ?> /><?php echo __('Hide Default', 'wp-nested-pages') . ' ' . $type->label . ' ' . __('Link', 'wp-nested-pages'); ?></label>
						</div>
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
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][disable_nesting]" value="true" <?php if ( $type->disable_nesting ) echo 'checked '; ?>/><?php echo __('Disable Nesting for', 'wp-nested-pages') . ' ' . $type->label; ?></label>
						</div>
					</div><!-- .row -->
				</li>
				<?php endif; ?>
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
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][thumbnails][display]" value="true" <?php if ( $thumbnails_enabled ) echo 'checked'; ?> data-enable-thumbnails /><?php echo __('Display Thumbnails for', 'wp-nested-pages') . ' ' . $type->label; ?></label>
							
							<div class="thumbnail-options" data-thumbnail-options <?php if ( !$thumbnails_enabled ) echo 'style="display:none;"'; ?>>
								<label><?php _e('Thumbnail Source', 'wp-nested-pages'); ?></label>
								<select name="nestedpages_posttypes[<?php echo $type->name; ?>][thumbnails][size]">
								<?php
								foreach ( $thumbnail_sizes as $size ){
									echo '<option value="' . $size . '"';
									if ( $size == $thumbnail_source ) echo ' selected';
									echo '>' . $size . '</option>';
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
				</li><!-- .thumbnails -->

				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Enable Admin Columns', 'wp-nested-pages'); ?></strong><br>
							<?php _e('If this post type has custom columns enabled through the manage_{$posts}_column filter, they may be enabled in the nested interface.', 'wp-nested-pages'); ?> <a href="https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns" target="_blank"><?php _e('More Information', 'wp-nested-pages'); ?></a>
							</p>
						</div>
						<div class="field">
							<?php 
							$all_columns = $this->post_type_repo->getCustomColumns($type->name, false, true);
							$selected_columns = ( isset($type->columns) ) ? $type->columns : array();
							?>
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][columns_enabled]" value="true" <?php if ( $type->columns_enabled ) echo 'checked '; ?> data-enable-columns />
							<?php echo __('Enable Columns for', 'wp-nested-pages') . ' ' . $type->label; ?>
							</label>
							<div class="columns">
								<?php if ( $all_columns ) : ?>
								<h5><?php _e('Check to columns to include. (Drag and drop to reorder columns)', 'wp-nested-pages'); ?></h5>
								<div class="custom-field-group">
									<ul data-columns-sortable>
										<?php
											$out = "";
											// Selected Columns (custom ordering)
											foreach ( $selected_columns as $name => $label ) :
												$out .= '<li>';
												$out .= '<label>';
												$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][columns][' . $name . ']" value="' . $label . '"';
												if ( array_key_exists($name, $type->columns) ) $out .= ' checked';
												$out .= ' />' . $label;
												$out .= '</label>';
												$out .= '</li>';
											endforeach;

											// Remaining Columns
											foreach ( $all_columns as $name => $label ) :
												if ( array_key_exists($name, $type->columns) ) continue;
												$out .= '<li>';
												$out .= '<label>';
												$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][columns][' . $name . ']" value="' . $label . '" />' . $label;
												$out .= '</label>';
												$out .= '</li>';
											endforeach;
											echo $out;
										?>
									</ul>
								</div><!-- .custom-field-group -->
								<?php else : ?>
									<?php _e('This post type does not have any custom columns available.', 'wp-nested-pages'); ?>
								<?php endif; ?>
							</div><!-- .standard-fields -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li><!-- .custom columns -->

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
												$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][standard_fields][standard][' . $name . ']" value="true"';
												if ( $name == 'hide_taxonomies' ) $out .= ' data-hide-taxonomies';
												if ( $this->post_type_repo->fieldEnabled($type->name, 'standard', $name, 'standard_fields') ) $out .= ' checked';
												$out .= ' />' . $label;
												$out .= '</label>';
												$out .= '</li>';
											else : // Taxonomies
												foreach ( $label as $tax_name => $tax_label ) :
													$disabled = $this->post_type_repo->taxonomyDisabled($tax_name, $type->name);
													$out .= '<li data-taxonomy-single style="margin-left:20px;';
													if ( $this->post_type_repo->fieldEnabled($type->name, 'standard', 'hide_taxonomies', 'standard_fields') ) $out .= 'display:none;';
													$out .= '">';
													$out .= '<label>';
													$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][standard_fields][standard][taxonomies][' . $tax_name . ']" value="true"';
													if ( $disabled ) $out .= ' checked';
													$out .= ' />' . $tax_label;
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
										$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][custom_fields][acf][' . $field['key'] . ']" value="' . $field['type'] . '"'; 
										if ( $this->post_type_repo->fieldEnabled($type->name, 'acf', $field['key']) ) $out .= ' checked';
										$out .= '/>' . $field['label'] . ' (' . $field['type'] . ')';
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
			<?php  endif; ?>
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