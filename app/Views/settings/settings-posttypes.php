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
					<label><input type="checkbox" name="nestedpages_posttypes[<?php echo $type->name; ?>][custom_fields_enabled]" value="true" <?php if ( $type->custom_fields_enabled ) echo 'checked'; ?> /><?php _e('Configure Custom Fields', 'nestedpages'); ?></label>
				</li>
			</ul>
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