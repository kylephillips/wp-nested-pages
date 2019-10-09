<?php
$allowsorting = get_option('nestedpages_allowsorting', array());
if ( $allowsorting == "" ) $allowsorting = array();
$sync_status = ( $this->settings->menuSyncEnabled() ) ? __('Currently Enabled', 'wp-nested-pages') : __('Currently Disabled', 'wp-nested-pages');
settings_fields( 'nestedpages-general' ); 
?>
<table class="form-table">
<tr valign="top">
	<th scope="row"><?php _e('Nested Pages Version', 'wp-nested-pages'); ?></th>
	<td><strong><?php echo get_option('nestedpages_version'); ?></strong></td>
</tr>
<?php if ( !$this->settings->menusDisabled() ) : ?>
<tr valign="top">
	<th scope="row"><?php _e('Menu Name', 'wp-nested-pages'); ?></th>
	<td>
		<input type="text" name="nestedpages_menu" id="nestedpages_menu" value="<?php echo $this->menu->name; ?>">
		<p><em><?php _e('Important: Once the menu name has changed, theme files should be updated to reference the new name.', 'wp-nested-pages'); ?></em></p>
	</td>
</tr>
<?php endif; ?>
<tr valign="top">
	<th scope="row"><?php _e('Display Options', 'wp-nested-pages'); ?></th>
	<td>
		<p>
		<label>
			<input type="checkbox" name="nestedpages_ui[datepicker]" value="true" <?php if ( $this->settings->datepickerEnabled() ) echo 'checked'; ?> />
			<?php _e('Enable Date Picker in Quick Edit', 'wp-nested-pages'); ?>
		</label>
		</p>
		<p>
		<label>
			<input type="checkbox" name="nestedpages_ui[non_indent]" value="true" <?php if ( $this->settings->nonIndentEnabled() ) echo 'checked'; ?> />
			<?php _e('Use the classic (non-indented) hierarchy display.', 'wp-nested-pages'); ?>
		</label>
		</p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Menu Sync', 'wp-nested-pages'); ?></th>
	<td>
		<?php if ( !$this->settings->menusDisabled() ) : ?>
		<p data-menu-enabled-option data-menu-hide-checkbox>
		<label>
			<input type="checkbox" name="nestedpages_ui[hide_menu_sync]" value="true" <?php if ( $this->settings->hideMenuSync() ) echo 'checked'; ?> />
			<?php printf(__('Hide Menu Sync Checkbox (%s)', 'wp-nested-pages'), esc_html__($sync_status)); ?>
		</label>
		</p>
		<?php endif; ?>
		<p data-menu-enabled-option data-menu-disable-auto>
		<label>
			<input type="checkbox" name="nestedpages_ui[manual_menu_sync]" value="true" <?php if ( $this->settings->autoMenuDisabled() ) echo 'checked'; ?> data-menu-disable-auto-checkbox />
			<?php _e('Manually sync menu.', 'wp-nested-pages'); ?>
		</label>
		</p>
		<p>
		<label>
			<input type="checkbox" name="nestedpages_ui[manual_page_order_sync]" value="true" <?php if ( $this->settings->autoPageOrderDisabled() ) echo 'checked'; ?> />
			<?php _e('Manually sync page order.', 'wp-nested-pages'); ?>
		</label>
		</p>
		<p>
		<label>
			<input type="checkbox" name="nestedpages_disable_menu" value="true" <?php if ( $this->settings->menusDisabled() ) echo 'checked'; ?> data-disable-menu-checkbox />
			<?php _e('Disable menu sync completely', 'wp-nested-pages'); ?>
		</label>
		</p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Allow Page Sorting', 'wp-nested-pages'); ?></th>
	<td>
		<?php foreach ( $this->user_repo->allRoles() as $role ) : ?>
		<label>
			<input type="checkbox" name="nestedpages_allowsorting[]" value="<?php echo $role['name']; ?>" <?php if ( in_array($role['name'], $allowsorting) ) echo 'checked'; ?> >
			<?php echo esc_html__($role['label']); ?>
		</label>
		<br />
		<?php endforeach; ?>
		<input type="hidden" name="nestedpages_menusync" value="<?php echo get_option('nestedpages_menusync'); ?>">
		<p><em><?php _e('Admins always have sorting ability.', 'wp-nested-pages'); ?></em></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Reset Plugin Settings', 'wp-nested-pages'); ?></th>
	<td>
		<div class="nestedpages-reset-settings">
			<p>
				<?php _e('Warning: Resetting plugin settings will remove all menu settings, post type customizations, role customizations and any other Nested Pages settings. These will be replaced with the default settings. This action cannot be undone.', 'wp-nested-pages'); ?>
			</p>
			<p>
				<button class="np-btn np-btn-trash" data-nestedpages-reset-settings><?php _e('Reset Nested Pages Settings', 'wp-nested-pages'); ?></button>
			</p>
		</div>
		<div class="nestedpages-reset-settings-complete" style="display:none;">
			<p><?php _e('Settings have been successfully reset.', 'wp-nested-pages'); ?></p>
		</div>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Reset User Preferences', 'wp-nested-pages'); ?></th>
	<td>
		<div class="nestedpages-reset-user-prefs">
			<p>
				<?php _e('Toggle states are saved for each user. This action will clear these preferences for all users. If PHP errors appear within the nested view after an update, this may help clear them.', 'wp-nested-pages'); ?>
			</p>
			<p>
				<button class="np-btn np-btn-trash" data-nestedpages-reset-user-prefs><?php _e('Reset User Preferences', 'wp-nested-pages'); ?></button>
			</p>
		</div>
		<div class="nestedpages-reset-user-prefs-complete" style="display:none;">
			<p><?php _e('User preferences have been successfully reset.', 'wp-nested-pages'); ?></p>
		</div>
	</td>
</tr>
</table>