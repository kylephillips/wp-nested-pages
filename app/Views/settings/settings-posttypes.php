<?php
$types = $this->getPostTypes();
settings_fields( 'nestedpages-posttypes' ); 
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
					<input type="checkbox" name="nestedpages_posttypes[]" value="<?php echo $type->name; ?>" <?php if ( $type->np_enabled ) echo 'checked'; ?> />
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:10px 0px;">
		<p style="font-style:oblique;">
			<?php _e('Note: Nesting features not enabled for non-hierarchical post types.', 'nestedpages'); ?>
		</p>
	</td>
</tr>