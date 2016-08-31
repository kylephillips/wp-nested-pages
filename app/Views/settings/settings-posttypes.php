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
			<h4><?php echo $type->label . ' ' . __('Settings', 'nestedpages'); ?></h4>
			<ul class="post-type-settings">
				<li>
					<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][replace_menu]" value="true" <?php if ( $type->replace_menu ) echo 'checked'; ?> /><?php _e('Replace Default Menu', 'nestedpages'); ?></label>
				</li>
				<li>
					<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][hide_default]" value="true" <?php if ( $type->hide_default ) echo 'checked'; ?> /><?php _e('Hide Default Link', 'nestedpages'); ?></label>
				</li>
				<?php if ( $type->hierarchical ) : ?>
				<li>
					<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][disable_nesting]" value="true" <?php if ( $type->disable_nesting ) echo 'checked '; ?>/><?php _e('Disable Nesting', 'nestedpages'); ?></label>
				</li>
				<?php endif; ?>
				<li>
					<label><input type="checkbox" data-toggle-nestedpages-sf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][standard_fields_enabled]" value="true" <?php if ( $type->standard_fields_enabled ) echo 'checked '; ?>/><?php _e('Configure Quick Edit Standard Fields', 'nestedpages'); ?></label>
				</li>
				<li>
					<label><input type="checkbox" data-toggle-nestedpages-cf-settings name="nestedpages_posttypes[<?php echo $type->name; ?>][custom_fields_enabled]" value="true" <?php if ( $type->custom_fields_enabled ) echo 'checked'; ?> /><?php _e('Configure Quick Edit Custom Fields', 'nestedpages'); ?></label>
				</li>
			</ul>
			<div class="custom-fields">
				<?php
					// Advanced Custom Fields
					$acf_fields = $this->acf_repo->getFieldsForPostType($type->name);
					if ( $acf_fields ) :
						$out = '<h4>' . __('Choose custom fields to include in Quick Edit forms.', 'nestedpages') . '</h4>';
						$out .= '<div class="custom-field-group">';
						$out .= '<p>' . __('Advanced Custom Fields', 'nestedpages') . '</p>';
						$out .= '<ul>';
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
			<div class="standard-fields">
				<h4><?php _e('Choose which standard fields to include in Quick Edit forms.', 'nestedpages'); ?></h4>
				<div class="custom-field-group">
				<ul>
					<?php
						$out = "";
						foreach ( $this->settings->standardFields() as $name => $label ) :
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
		</div><!-- .body -->
	</div><!-- .post-type -->
	<?php endforeach; ?>
</div><!-- .nestedpages-settings-posttypes -->

<div class="nestedpages-settings-disclaimers">
	<p style="font-style:oblique;font-size:13px;margin-bottom:15px;">
		<?php _e('Note: Nesting features not enabled for non-hierarchical post types.', 'nestedpages'); ?>
	</p>
	<p style="font-size:12px;margin-bottom:15px;">
		*<?php _e('If default menu is not replaced, an additional submenu item will be added for "Nested/Sort View"', 'nestedpages'); ?>
	</p>
	<p style="font-size:12px;">
		**<?php _e('<strong>Important:</strong> Changing page structures on live sites may effect SEO and existing inbound links. Limit URL structure changes on live sites by disabling nesting. Sorting within the current nesting structure will still be available. If nesting changes are made to a live site, it may help to add a 301 redirect from the old location to the new one.', 'nestedpages'); ?>
	</p>
</div>