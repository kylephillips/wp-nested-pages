<?php
$types = $this->getPostTypes();
settings_fields( 'nestedpages-posttypes' ); 

// $page_field_groups = acf_get_field_groups(array(
// 	'post_type' => 'page'
// ));
// $page_fields = acf_get_fields($page_field_groups[0]);
// var_dump($page_field_groups);
// var_dump($page_fields);

// $rendered = acf_render_fields( '71717', $page_fields, 'div' );
// if ( function_exists('the_field') ) :
// foreach($page_fields as $field)
// {
// 	$acf = new acf_field;
// 	$vfield = $acf->get_valid_field($field);
// 	var_dump($vfield);

// 	// acf_render_field($field);
// 	// $afields = acf_get_field_groups();
// 	// $fields = acf_get_fields( $page_fields );
			
// 	// acf_render_fields( 71717, $page_fields, 'div' );
// }
// endif;


?>
<tr valign="top">
	<td colspan="2" style="padding:0px;">
		<h3 style="margin-bottom:10px;"><?php _e('Enable Nested Pages for:', 'nestedpages'); ?></h3>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<table width="100%" class="nestedpages-settings-table">
			<thead>
				<th><?php _e('Post Type', 'nestedpages'); ?></th>
				<th><?php _e('Hierarchical', 'nestedpages'); ?></th>
				<th><?php _e('Enabled', 'nestedpages'); ?></th>
				<th><?php _e('Replace Default Menu', 'nestedpages'); ?>*</th>
				<th><?php _e('Hide Default Link', 'nestedpages'); ?>*</th>
				<th><?php _e('Disable Nesting', 'nestedpages'); ?>**</th>
				<th><?php _e('Quick Edit Custom Fields', 'nestedpages'); ?>**</th>
			</thead>
			<?php foreach ($types as $type) : ?>
			<tr>
				<td><?php echo $type->label; ?></td>
				<td>
					<?php if ( $type->hierarchical ) : ?>
					<i class="np-icon-yes"></i>
					<?php endif; ?>
				</td>
				<td>
					<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>]" value="<?php echo $type->name; ?>" <?php if ( $type->np_enabled ) echo 'checked'; ?> />
				</td>
				<td>
					<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][replace_menu]" value="true" <?php if ( $type->replace_menu ) echo 'checked'; ?> />
				</td>
				<td>
					<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][hide_default]" value="true" <?php if ( $type->hide_default ) echo 'checked'; ?> />
				</td>
				<td>
					<?php if ( $type->hierarchical ) : ?>
					<input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][disable_nesting]" value="true" <?php if ( $type->disable_nesting ) echo 'checked '; ?>/>
					<?php endif; ?>
				</td>
				<td>
					<?php
						$acf_fields = $this->acf_repo->getFieldsForPostType($type->name);
						if ( $acf_fields ) :
							$out = "";
							foreach($acf_fields as $field){
								$out .= '<p><label><input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][customfields][]" value="' . $field['key'] . '" /> ' . $field['label'] . ' (' . $field['type'] . ')</label></p>';
							}
							echo $out;
						endif;
						// var_dump($acf_fields);
						// $fields = $this->post_type_repo->getCustomFields($type->name);
						// if ( $fields ){
						// 	foreach($fields as $field){
						// 		echo '<label><input type="checkbox" name="nestedpages_posttypes[' . $type->name . '][customfields]" value="' . $field . '" />' . $field . '</label>';
						// 	}
						// }
						?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:10px 0px;">
		<p style="font-style:oblique;font-size:13px;margin-bottom:15px;">
			<?php _e('Note: Nesting features not enabled for non-hierarchical post types.', 'nestedpages'); ?>
		</p>
		<p style="font-size:12px;margin-bottom:15px;">
			*<?php _e('If default menu is not replaced, an additional submenu item will be added for "Nested/Sort View"', 'nestedpages'); ?>
		</p>
		<p style="font-size:12px;">
			**<?php _e('<strong>Important:</strong> Changing page structures on live sites may effect SEO and existing inbound links. Limit URL structure changes on live sites by disabling nesting. Sorting within the current nesting structure will still be available. If nesting changes are made to a live site, it may help to add a 301 redirect from the old location to the new one.', 'nestedpages'); ?>
		</p>
	</td>
</tr>