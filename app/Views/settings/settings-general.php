<?php
$allowsorting = get_option('nestedpages_allowsorting', []);
$allowsortview = $this->settings->sortViewEnabled();
if ( $allowsorting == "" ) $allowsorting = [];
$sync_status = ( $this->settings->menuSyncEnabled() ) ? __('Currently Enabled', 'wp-nested-pages') : __('Currently Disabled', 'wp-nested-pages');
?>
<div class="nestedpages-settings-general-wrapper">
	<div class="nestedpages-settings-table">
		<form method="post" enctype="multipart/form-data" action="options.php">
		<?php settings_fields( 'nestedpages-general' ); ?>
		<div class="row-container">
			<div class="row">
				<div class="description">
					<p><strong><?php _e('Nested Pages Version', 'wp-nested-pages'); ?></strong></p>
				</div>
				<div class="field">
					<p><?php echo get_option('nestedpages_version'); ?></p>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Display Options', 'wp-nested-pages'); ?></strong></p>
				</div>
				<div class="field">
					<p><label>
						<input type="checkbox" name="nestedpages_ui[datepicker]" value="true" <?php if ( $this->settings->datepickerEnabled() ) echo 'checked'; ?> />
						<?php _e('Enable Date Picker in Quick Edit', 'wp-nested-pages'); ?>
					</label></p>
					<p><label>
						<input type="checkbox" name="nestedpages_ui[non_indent]" value="true" <?php if ( $this->settings->nonIndentEnabled() ) echo 'checked'; ?> />
						<?php _e('Use the classic (non-indented) hierarchy display.', 'wp-nested-pages'); ?>
					</label></p>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Menu Sync', 'wp-nested-pages'); ?></strong></p>
				</div>
				<div class="field">
					<?php if ( !$this->settings->menusDisabled() ) : ?>
					<p data-menu-enabled-option data-menu-hide-checkbox>
					<label>
						<input type="checkbox" name="nestedpages_ui[hide_menu_sync]" value="true" <?php if ( $this->settings->hideMenuSync() && !$this->settings->menusDisabled() ) echo 'checked'; ?> />
						<?php printf(__('Hide Menu Sync Checkbox (%s)', 'wp-nested-pages'), esc_html($sync_status)); ?>
					</label>
					</p>
					<p data-menu-enabled-option data-menu-private>
					<label>
						<input type="checkbox" name="nestedpages_ui[include_private]" value="true" <?php if ( $this->settings->privateMenuEnabled() && !$this->settings->menusDisabled() ) echo 'checked'; ?> />
						<?php _e('Include private items in the menu.', 'wp-nested-pages'); ?>
					</label>
					</p>
					<?php endif; ?>
					<p data-menu-enabled-option data-menu-disable-auto>
					<label>
						<input type="checkbox" name="nestedpages_ui[manual_menu_sync]" value="true" <?php if ( $this->settings->autoMenuDisabled() && !$this->settings->menusDisabled() ) echo 'checked'; ?> data-menu-disable-auto-checkbox />
						<?php _e('Manually sync menu.', 'wp-nested-pages'); ?><br>
					</label>
					</p>
					<p>
					<label>
						<input type="checkbox" name="nestedpages_ui[manual_page_order_sync]" value="true" <?php if ( $this->settings->autoPageOrderDisabled() ) echo 'checked'; ?> />
						<?php _e('Manually sync page order.', 'wp-nested-pages'); ?>
					</label>
					</p>
					<p data-menu-enabled-option data-menu-disable-auto>
					<label>
						<input type="checkbox" name="nestedpages_ui[menu_sync_default_hide]" value="true" <?php if ( $this->settings->defaultHideInNav() ) echo 'checked'; ?> />
						<?php _e('Default new pages to hide in nav menu.', 'wp-nested-pages'); ?>
					</label>
					</p>
					<p>
					<label>
						<input type="checkbox" name="nestedpages_disable_menu" value="true" <?php if ( $this->settings->menusDisabled() ) echo 'checked'; ?> data-disable-menu-checkbox />
						<?php _e('Disable menu sync completely', 'wp-nested-pages'); ?>
					</label>
					</p>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Allow Page Sorting', 'wp-nested-pages'); ?></strong></p>
					<p><?php _e('Page sorting capability is also controlled through the nestedpages_sorting_$type capability', 'wp-nested-pages'); ?></p>
				</div>
				<div class="field">
					<?php foreach ( $this->user_repo->allRoles() as $role ) : ?>
					<label>
						<input type="checkbox" name="nestedpages_allowsorting[]" value="<?php echo $role['name']; ?>" <?php if ( in_array($role['name'], $allowsorting) ) echo 'checked'; ?> >
						<?php echo esc_html($role['label']); ?>
					</label>
					<br />
					<?php endforeach; ?>
					<input type="hidden" name="nestedpages_menusync" value="<?php echo get_option('nestedpages_menusync'); ?>">
					<p><em><?php _e('Admins always have sorting ability.', 'wp-nested-pages'); ?></em></p>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Allow Sort View', 'wp-nested-pages'); ?></strong></p>
					<p><?php _e('Sort view access is also filterable through the nestedpages_sort_view_$type filter.', 'wp-nested-pages'); ?></p>
				</div>
				<div class="field">
					<input type="hidden" name="nestedpages_allowsortview[]" value="<?php echo 'administrator'; ?>" >
					<?php foreach ( $this->user_repo->allRoles(['Administrator']) as $role ) : ?>
					<label>
						<?php
						$checked = false;
						if ( !$allowsortview ) $checked = true;
						if ( is_array($allowsortview) && in_array($role['name'], $allowsortview) ) $checked = true;
						?>
						<input type="checkbox" name="nestedpages_allowsortview[]" value="<?php echo $role['name']; ?>" <?php if ( $checked ) echo 'checked'; ?> >
						<?php echo esc_html($role['label']); ?>
					</label>
					<br />
					<?php endforeach; ?>
					<p><em><?php _e('Admins may always view the sort view.', 'wp-nested-pages'); ?></em></p>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Reset Plugin Settings', 'wp-nested-pages'); ?></strong></p>
					<p><?php _e('Warning: Resetting plugin settings will remove all menu settings, post type customizations, role customizations and any other Nested Pages settings. These will be replaced with the default settings. This action cannot be undone.', 'wp-nested-pages'); ?></p>
				</div>
				<div class="field">
					<p><button class="np-btn np-btn-trash" data-nestedpages-reset-settings><?php _e('Reset Nested Pages Settings', 'wp-nested-pages'); ?></button></p>
					<div class="nestedpages-reset-settings-complete" style="display:none;">
						<p><?php _e('Settings have been successfully reset.', 'wp-nested-pages'); ?></p>
					</div>
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="description">
					<p><strong><?php _e('Reset User Preferences', 'wp-nested-pages'); ?></strong></p>
					<p><?php _e('Toggle states are saved for each user. This action will clear these preferences for all users. If PHP errors appear within the nested view after an update, this may help clear them.', 'wp-nested-pages'); ?></p>
				</div>
				<div class="field">
					<div class="nestedpages-reset-user-prefs">
						<p>
							<button class="np-btn np-btn-trash" data-nestedpages-reset-user-prefs><?php _e('Reset User Preferences', 'wp-nested-pages'); ?></button>
						</p>
					</div>
					<div class="nestedpages-reset-user-prefs-complete" style="display:none;">
						<p><?php _e('User preferences have been successfully reset.', 'wp-nested-pages'); ?></p>
					</div>
				</div>
			</div><!-- .row -->
			<div class="row submit">
				<?php submit_button(); ?>
			</div>
		</div><!-- .row-container -->
		</form>
	</div><!-- .nestedpages-settings-table -->

	<div class="nestedpages-settings-support">
		<div class="inner">
			<h3><?php _e('Show Some Love!', 'wp-nested-pages'); ?></h3>
			<h4><?php _e('Sponsor this Plugin', 'wp-nested-pages'); ?></h4>
			<p><?php _e('You can help support the continued development and upkeep of this plugin by sponsoring on Github. Whether you are an individual using this on a side project, a business, or an agency using this plugin across multiple sites… every bit helps!', 'wp-nested-pages'); ?></p>
			<p style="margin-bottom:0;"><a href="https://github.com/sponsors/kylephillips" target="_blank" class="support-button"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path class="heart" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg><?php _e('Sponsor on Github', 'wp-nested-pages'); ?></a></p>
			<p class="or"><?php _e('- or Donate through Paypal -', 'wp-nested-pages'); ?></p>
			<form class="paypal-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="CDX8VVMMMMLAU" />
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
			</form>
		</div><!-- .inner -->
	</div><!-- .nestedpages-settings-table -->

</div><!-- .nestedpages-general-wrapper -->
