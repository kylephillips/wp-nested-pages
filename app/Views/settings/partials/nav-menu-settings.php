<?php 
/**
* Outputs the Admin Navigation Customization Settings, with specific settings for each user role
*/
$c = 1;
foreach ( $this->admin_menu_settings->roles as $role ) :
	$menu = $this->admin_menu_settings->np_menu_original;
	$menu = ( isset($menu[$role['name']]) ) ? $menu[$role['name']] : $menu['default'];
	$role_capabilities = $this->user_repo->getSingleRoleCapabilities($role['name']);
	$hidden = $this->settings->adminMenuHidden($role['name']);
	if ( !$hidden ) $hidden = [];
	?>
	<div class="np-menu-customization"	<?php if ( $c == 1 ) echo ' style="display:block;"'; ?>	data-np-nav-menu-customization="menu_role_<?php echo $role['name']; ?>">

	<?php include(NestedPages\Helpers::view('settings/partials/nav-menu-settings/header')); ?>
	
	<ul class="np-nav-menu-settings" data-np-sortable-admin-nav>
	
	<?php
	$i = 0; // Counter for order
	foreach ( $menu as $menu_item ) :

		// This role doesn't have access to this item
		if ( !array_key_exists($menu_item[1], $role_capabilities) || !$role_capabilities[$menu_item[1]] ) continue; 

		$item_data = $this->admin_menu_settings->menuItemData($menu_item, $role);
		?>
		<li class="np-nav-preview <?php if ( in_array($item_data['id'], $hidden) ) echo 'disabled'; ?><?php if ( $item_data['separator'] ) echo ' separator';?>" <?php if ( $item_data['separator'] ) echo 'data-np-separator-row'; ?>>
			<div class="menu-item">
				<div class="submenu-toggle">
					<?php if ( $item_data['submenu'] ) : ?>
					<a href="#" data-np-nav-menu-customization-submenu-toggle><span class="arrow"></span></a>
					<?php endif; ?>
				</div>
				<div class="handle">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class=" np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
				</div>
				<div class="title"><div class="menu-icon dashicons-before <?php echo $item_data['icon']; ?>"></div>
				<p>
					<?php if ( $item_data['separator'] ) : ?>
					<?php 
					echo ( $menu_item[4] !== 'wp-menu-separator woocommerce' ) ? __('Separator', 'wp-nested-pages') : __('Separator (WooCommerce)', 'wp-nested-pages');					
					?>
					<button class="button button-small details-button" data-np-remove-separator-button><?php _e('Remove', 'wp-nested-pages'); ?></button>
					<?php else : ?>
					<input type="text" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][label]" value="<?php if ( $item_data['custom_label'] ) echo esc_attr($item_data['custom_label']); ?>" placeholder="<?php esc_html_e($item_data['original_text']); ?>" class="menu-title-field" />
					<button class="button button-small details-button" data-np-extra-options-button><?php _e('Details', 'wp-nested-pages');?></button>
					<?php endif; ?>
				</p>
				</div><!-- .title -->
			</div><!-- .menu-item -->
			<?php if ( !$item_data['separator'] ) : ?>
			<div class="hide-checkbox">
				<?php if ( $item_data['original_link'] !== 'options-general.php' ) : ?>
				<input type="checkbox" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][hidden]" value="<?php echo $item_data['id']; ?>" data-nestedpages-admin-nav-item-checkbox	<?php if ( in_array($item_data['id'], $hidden) ) echo 'checked'; ?> />
				<?php endif; ?>
			</div>
			<?php else : ?>
			<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>]" value="true">
			<?php endif; ?>
			<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][order]" value="<?php echo $i; ?>" data-np-menu-order>
			<?php if ( !$item_data['separator'] ) : ?>
			<div class="np-extra-options" data-np-extra-options>
				<div class="half">
					<label><?php _e('Icon CSS Class', 'wp-nested-pages'); ?> <em>(<a href="https://developer.wordpress.org/resource/dashicons/#admin-site" target="_blank"><?php _e('Reference', 'wp-nested-pages'); ?></a>)</em></label>
					<input type="text" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][icon]" value="<?php if ( $item_data['custom_icon'] ) echo esc_attr(sanitize_text_field($item_data['custom_icon'])); ?>" placeholder="<?php echo sanitize_text_field($item_data['original_icon']); ?>" />
				</div><!-- .half -->
				<div class="half right" style="display:none;">
					<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][link]" value="<?php echo esc_attr($item_data['original_link']); ?>">
					<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][original_link]" value="<?php echo esc_attr($item_data['original_link']); ?>">
				</div>
			</div><!-- .np-extra-options -->
			<?php endif; ?>
			
			<?php if ( $item_data['submenu'] ) : ?>
			<ul class="submenu-listing" data-np-sortable-admin-subnav>
				<?php 
				$si = 0;
				foreach ( $item_data['submenu_items'] as $submenu ) : 
				$label = ( $item_data['has_custom_submenu'] ) ? $submenu['label'] : $submenu[0];
				$role_name = ( $item_data['has_custom_submenu'] ) ?  $submenu['role'] : $submenu[1];
				$link = ( $item_data['has_custom_submenu'] ) ? $submenu['link'] : $submenu[2];
				$hidden_sub = ( $item_data['has_custom_submenu'] && isset($submenu['hidden']) && $submenu['hidden'] == 'true' ) ? true : false;
				if ( !array_key_exists($role_name, $role_capabilities) || !$role_capabilities[$role_name] ) continue; // This role doesn't have access to this item
				?>
				<li class="np-nav-preview <?php if ( $hidden_sub ) echo 'disabled'; ?> submenu-item" data-np-sortable-admin-nav>
					<div class="menu-item">
						<div class="handle">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class=" np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
						</div>
						<div class="title"><p><?php echo $label; ?></p></div>
						<div class="hide-checkbox">
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][submenu][<?php echo $si; ?>][label]" value="<?php echo esc_attr($label); ?>" />
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][submenu][<?php echo $si; ?>][role]" value="<?php echo esc_attr($role_name); ?>" />
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][submenu][<?php echo $si; ?>][link]" value="<?php echo esc_attr($link); ?>" />
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][submenu][<?php echo $si; ?>][order]" value="<?php echo $si; ?>" data-np-submenu-order>
							<?php if ( $link !== 'nested-pages-settings' ) : ?>
							<input type="checkbox" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $item_data['id']; ?>][submenu][<?php echo $si; ?>][hidden]" value="true" data-nestedpages-admin-nav-item-checkbox <?php if ( $hidden_sub )  echo 'checked'; ?>/>
							<?php endif; ?>
						</div>
					</div><!-- .menu-item -->
				</li>
				<?php $si++; endforeach; ?>
			</ul>
			<?php endif; // Submenu ?>
		</li>
		<?php $i++; endforeach; ?>
	</ul>	
	</div><!-- .np-menu-customization -->
<?php $c++; endforeach;