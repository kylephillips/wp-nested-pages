<?php 
$this->admin_menu_settings = new NestedPages\Config\AdminMenuSettings;
settings_fields( 'nestedpages-admincustomization' ); ?>
<h3><?php _e('Select Items to Customize', 'wp-nested-pages'); ?></h3>
<div class="nestedpages-settings-table">

	<!-- Nav Menu -->
	<div class="row-container">
		<div class="head">
			<div class="checkbox">
				<input type="checkbox" name="nestedpages_admin[enabled_menu]" value="true" <?php if ( $this->settings->adminCustomEnabled('enabled_menu') ) echo 'checked'; ?> id="side_menu" />
			</div>
			<label for="side_menu"><?php _e('Admin Menu', 'wp-nested-pages'); ?></label>
			<a href="#" class="button" data-toggle-nestedpages-pt-settings><?php _e('Settings', 'wp-nested-pages'); ?></a>
		</div><!-- .head -->
		<div class="body">
			<ul class="settings-details">
			<li>
				<div class="row">
					<div class="description">
						<p><strong><?php _e('Menu Items', 'wp-nested-pages'); ?></strong><br><?php _e('Reorder, hide and rename admin menu items by user role. These changes do not effect actual permissions, only menu appearance.'); ?></p>
						<p><strong><?php _e('Important: Plugin Menu Items ', 'wp-nested-pages'); ?></strong><br><?php _e('Some plugins may add menu items on activation. These new menu items will not display until the Nested Pages Admin Menu has been configured with these items.', 'wp-nested-pages'); ?></p>
						<p>
							<button class="np-btn np-btn-trash" data-nestedpages-reset-admin-menu><?php _e('Reset Admin Menu Settings', 'wp-nested-pages'); ?></button>
						</p>
					</div>
					<div class="field">
						<?php include(NestedPages\Helpers::view('settings/partials/nav-menu-settings')); ?>
					</div><!-- .field -->
				</div><!-- .row -->
			</li>
			</ul><!-- .settings-details -->
		</div><!-- .body -->
	</div><!-- .row-container, nav menu -->

</div><!-- .nestedpages-settings-table -->