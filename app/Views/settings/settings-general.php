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
		<input type="text" name="nestedpages_menu" id="nestedpages_menu" value="<?php echo esc_attr($this->menu->name); ?>">
		<p><em><?php _e('Important: Once the menu name has changed, theme files should be updated to reference the new name.', 'wp-nested-pages'); ?></em></p>
	</td>
</tr>
<?php endif; ?>
<tr valign="top">
	<th scope="row"><?php _e('Display Options', 'wp-nested-pages'); ?></th>
	<td>
		<label>
			<input type="checkbox" name="nestedpages_ui[datepicker]" value="true" <?php if ( $this->settings->datepickerEnabled() ) echo 'checked'; ?> />
			<?php _e('Enable Date Picker in Quick Edit', 'wp-nested-pages'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Show favorites', 'wp-nested-pages'); ?></th>
	<td>
		<label>
			<input type="checkbox" name="nestedpages_ui[favorites]" value="true" <?php if ( $this->settings->favoritesEnabled() ) echo 'checked'; ?> />
			<?php _e('Enable favorite pages functionality', 'wp-nested-pages'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Menu Sync', 'wp-nested-pages'); ?></th>
	<td>
		<p data-menu-enabled-option data-menu-hide-checkbox>
		<label>
			<input type="checkbox" name="nestedpages_ui[hide_menu_sync]" value="true" <?php if ( $this->settings->hideMenuSync() ) echo 'checked'; ?> />
			<?php _e('Hide Menu Sync Checkbox', 'wp-nested-pages'); ?> (<?php echo esc_html($sync_status); ?>)
		</label>
		</p>
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
			<?php echo esc_html($role['label']); ?>
		</label>
		<br />
		<?php endforeach; ?>
		<input type="hidden" name="nestedpages_menusync" value="<?php echo get_option('nestedpages_menusync'); ?>">
		<p><em><?php _e('Admins always have sorting ability.', 'wp-nested-pages'); ?></em></p>
	</td>
</tr>
</table>