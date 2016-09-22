<?php
$types = $this->getPostTypes();
settings_fields( 'nestedpages-posttypes' ); 
?>

<h3><?php _e('Enable Nested Pages for:', 'nestedpages'); ?></h3>

<div class="nestedpages-settings-posttypes">
	<?php foreach ($types as $type) : ?>
	<div class="post-type">
		<div class="head">
			<div class="checkbox">
				<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>]" value="<?php echo $type->name; ?>" <?php if ( $type->np_enabled ) echo 'checked'; ?> data-nestedpages-pt-checbox id="post-type-<?php echo $type->name; ?>" />
			</div>
			<label for="post-type-<?php echo $type->name; ?>">
				<?php 
					echo $type->label; 
					if ( $type->hierarchical ) echo ' <em>(' . __('Hierarchical', 'nestedpages') . ')</em>';
				?>
			</label>
			<a href="#" class="button" data-toggle-nestedpages-pt-settings><?php _e('Settings', 'nestedpages'); ?></a>
		</div><!-- .head -->
		<div class="body">
			<ul class="post-type-settings">
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Replace Default Menu', 'nestedpages'); ?>*</strong><br />
							<?php _e('Replace the default top-level item with the nested view link.', 'nestedpages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][replace_menu]" value="true" <?php if ( $type->replace_menu ) echo 'checked'; ?> /><?php echo __('Replace Default') . ' ' . $type->label . ' ' . __('Menu', 'nestedpages'); ?></label>
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Remove Default Link', 'nestedpages'); ?></strong><br />
							<?php _e('If the default menu is replaced, a link to the default view will be added. Select this to remove the link', 'nestedpages'); ?>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][hide_default]" value="true" <?php if ( $type->hide_default ) echo 'checked'; ?> /><?php echo __('Hide Default', 'nestedpages') . ' ' . $type->label . ' ' . __('Link', 'nestedpages'); ?></label>
						</div>
					</div><!-- .row -->
				</li>
				<?php if ( $type->hierarchical ) : ?>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Disable Nesting', 'nestedpages'); ?>**</strong><br>
							<?php _e('To disable nesting on hierarchical post types, select this option.', 'nestedpages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][disable_nesting]" value="true" <?php if ( $type->disable_nesting ) echo 'checked '; ?>/><?php echo __('Disable Nesting for', 'nestedpages') . ' ' . $type->label; ?></label>
						</div>
					</div><!-- .row -->
				</li>
				<?php endif; ?>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Configure Standard Fields', 'nestedpages'); ?></strong><br>
							<?php _e('Remove standard fields from the quick edit form.', 'nestedpages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" data-toggle-nestedpages-sf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][standard_fields_enabled]" value="true" <?php if ( $type->standard_fields_enabled ) echo 'checked '; ?>/><?php _e('Configure Standard Fields', 'nestedpages'); ?></label>

							<div class="standard-fields">
								<h5><?php _e('Check to remove from Quick Edit.', 'nestedpages'); ?></h5>
								<div class="custom-field-group">
								<ul>
									<?php
										$out = "";
										foreach ( $this->settings->standardFields($type->name) as $name => $label ) :
											$out .= '<li>';
											$out .= '<label>';
											$out .= '<input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][standard_fields][standard][' . $name . ']" value="true"';
											if ( $this->post_type_repo->fieldEnabled($type->name, 'standard', $name, 'standard_fields') ) $out .= ' checked';
											$out .= ' />' . $label;
											$out .= '</label>';
											$out .= '</li>';
										endforeach;
										echo $out;
									?>
								</ul>
								</div><!-- .custom-field-group -->
							</div><!-- .standard-fields -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
				<li>
					<div class="row">
						<div class="description">
							<p><strong><?php _e('Configure Custom Fields', 'nestedpages'); ?></strong><br>
							<?php _e('Set which custom fields display in the quick edit form.', 'nestedpages'); ?></p>
						</div>
						<div class="field">
							<label><input type="checkbox" data-toggle-nestedpages-cf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][custom_fields_enabled]" value="true" <?php if ( $type->custom_fields_enabled ) echo 'checked'; ?> /><?php _e('Configure Custom Fields', 'nestedpages'); ?></label>

							<div class="custom-fields">
							<h5><?php _e('Check to Include in Quick Edit.', 'nestedpages'); ?></h5>
							<?php
								// Advanced Custom Fields
								$acf_fields = $this->acf_repo->getFieldsForPostType($type->name);
								if ( $acf_fields ) :
									$out = '<div class="custom-field-group">';
									$out .= '<p>' . __('Advanced Custom Fields', 'nestedpages') . '</p>';
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
									echo __('No ACF Fields configured for this post type', 'nestedpages');
								endif;
								?>
							</div><!-- .custom-fields -->
						</div><!-- .field -->
					</div><!-- .row -->
				</li>
			</ul>
		</div><!-- .body -->
	</div><!-- .post-type -->
	<?php endforeach; ?>
</div><!-- .nestedpages-settings-posttypes -->

<div class="nestedpages-settings-disclaimers">
	<p style="font-size:12px;margin-bottom:15px;">
		*<?php _e('If default menu is not replaced, an additional submenu item will be added for "Nested/Sort View"', 'nestedpages'); ?>
	</p>
	<p style="font-size:12px;">
		**<?php _e('<strong>Important:</strong> Changing page structures on live sites may effect SEO and existing inbound links. Limit URL structure changes on live sites by disabling nesting. Sorting within the current nesting structure will still be available. If nesting changes are made to a live site, it may help to add a 301 redirect from the old location to the new one.', 'nestedpages'); ?>
	</p>
</div>