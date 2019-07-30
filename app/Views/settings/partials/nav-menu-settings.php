<?php 
/**
* Outputs the Admin Navigation Customization Settings, with specific settings for each user role
*/
$roles = $this->user_repo->allRoles(null, true); 
$nav_menu_options = $this->settings->adminCustomEnabled('nav_menu_options');
global $np_menu_original;
global $np_submenu_original;
global $menu;
$c = 1;
foreach ( $roles as $role ) :
	$menu = $np_menu_original;
	$menu = ( isset($menu[$role['name']]) ) ? $menu[$role['name']] : $menu['default'];
	$role_capabilities = $this->user_repo->getSingleRole($role['name']);
	$role_capabilities = $role_capabilities['capabilities'];
	$hidden = $this->settings->adminMenuHidden($role['name']);
	if ( !$hidden ) $hidden = [];
	?>
	<div class="np-menu-customization"	<?php if ( $c == 1 ) echo ' style="display:block;"'; ?>	data-np-nav-menu-customization="menu_role_<?php echo $role['name']; ?>">

	<div class="np-menu-customization-header">
		<div class="role-select">
			<select data-np-nav-menu-user-role-select>
				<?php
					$s = 0;
					foreach ( $roles as $select_role ){
						$out = '';
						$out .= '<option value="menu_role_' . $select_role['name'] . '"';
						if ( $s == 0 ) $out .= ' selected="selected"';
						$out .= '>' . $select_role['label'] . '</option>';
						echo $out;
						$s++;
					}
				?>
			</select>
		</div><!-- .role-select -->
		<div class="new">
			<div class="nestedpages-dropdown" data-dropdown>
				<a href="#" class="nestedpages-dropdown-toggle" data-dropdown-toggle><?php _e('New', 'wp-nested-pages'); ?> <span class="np-caret"></span></a>
				<div class="nestedpages-dropdown-content right" data-dropdown-content>
					<ul>
						<li><a href="#" data-np-add-separator-button><?php _e('Separator', 'wp-nested-pages'); ?></a></li>
					</ul>
				</div>
			</div>
		</div><!-- .new -->
		<div class="hide"><?php _e('Hide', 'wp-nested-pages'); ?></div>
	</div><!-- .np-menu-customization-header -->
	
	<ul class="np-nav-menu-settings" data-np-sortable-admin-nav>
	
	<?php
	$i = 0; // Counter for order
	foreach ( $menu as $menu_item ) :

		$separator = ( $menu_item[0] == '' && isset($menu_item[4]) && $menu_item[4] == 'wp-menu-separator' ) ? true : false;

		// WooCommerce Separator
		if ( isset($menu_item[4]) && $menu_item[4] == 'wp-menu-separator woocommerce' ) $separator = true;
		if ( !array_key_exists($menu_item[1], $role_capabilities) || !$role_capabilities[$menu_item[1]] ) continue; // This role doesn't have access to this item
		$custom_item = ( isset($menu_item[7]) && $menu_item[7] == 'custom-item' ) ? true : false;
		$id = $menu_item[2];

		// Set the Label
		$text = strip_tags(preg_replace('#<span[^>]*>(.*)</span>#isU','', $menu_item[0]));
		$original_text = $text;
		$custom_label = null;
		if ( isset($nav_menu_options[$role['name']][$id]['label']) && $nav_menu_options[$role['name']][$id]['label'] !== '' )
			$custom_label = $nav_menu_options[$role['name']][$id]['label'];
		if ( $custom_item && !$custom_label ) $custom_label = strip_tags(preg_replace('#<span[^>]*>(.*)</span>#isU','', $menu_item[0]));
		if ( $separator ) $custom_label = __('Separator', 'wp-nested-pages');
		if ( $custom_label ) $text = $custom_label;

		// Set the Icon
		$icon = ( isset($menu_item[6]) && $menu_item[6] !== '' ) ? $menu_item[6] : 'dashicons-admin-post';
		$original_icon = $icon;
		$custom_icon = null;
		if ( isset($nav_menu_options[$role['name']][$id]['icon']) && $nav_menu_options[$role['name']][$id]['icon'] !== '' )
			$custom_icon = $nav_menu_options[$role['name']][$id]['icon'];
		if ( $custom_item && !$custom_icon ) $custom_icon = $menu_item[6];
		if ( $custom_icon ) $icon = $custom_icon;

		// Set the Link
		$original_link = $menu_item[2];
		?>
		<li class="np-nav-preview <?php if ( in_array($id, $hidden) ) echo 'disabled'; ?><?php if ( $separator ) echo ' separator';?>" <?php if ( $separator ) echo 'data-np-separator-row'; ?>>
			<div class="menu-item">
				<div class="submenu-toggle">
					<?php if ( isset($menu_item['submenu']) ) : ?>
					<a href="#" data-np-nav-menu-customization-submenu-toggle><span class="arrow"></span></a>
					<?php endif; ?>
				</div>
				<div class="handle">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class=" np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
				</div>
				<div class="title"><div class="menu-icon dashicons-before <?php echo $icon; ?>"></div>
				<p>
					<?php if ( $separator ) : ?>
					<?php 
					echo ( $menu_item[4] !== 'wp-menu-separator woocommerce' ) ? __('Separator', 'wp-nested-pages') : __('Separator (WooCommerce)', 'wp-nested-pages');					
					?>
					<button class="button button-small details-button" data-np-remove-separator-button><?php _e('Remove', 'wp-nested-pages'); ?></button>
					<?php else : ?>
					<input type="text" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][label]" value="<?php if ( $custom_label ) echo $custom_label; ?>" placeholder="<?php echo esc_html($original_text); ?>" class="menu-title-field" />
					<button class="button button-small details-button" data-np-extra-options-button><?php _e('Details', 'wp-nested-pages');?></button>
					<?php endif; ?>
				</p>
				</div><!-- .title -->
			</div><!-- .menu-item -->
			<?php if ( !$separator ) : ?>
			<div class="hide-checkbox">
				<input type="checkbox" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][hidden]" value="<?php echo $id; ?>" data-nestedpages-admin-nav-item-checkbox	<?php if ( in_array($id, $hidden) ) echo 'checked'; ?> />
			</div>
			<?php else : ?>
			<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>]" value="true">
			<?php endif; ?>
			<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][order]" value="<?php echo $i; ?>" data-np-menu-order>
			<?php if ( !$separator ) : ?>
			<div class="np-extra-options" data-np-extra-options>
				<div class="half">
					<label><?php _e('Icon CSS Class', 'wp-nested-pages'); ?> <em>(<a href="https://developer.wordpress.org/resource/dashicons/#admin-site" target="_blank"><?php _e('Reference', 'wp-nested-pages'); ?></a>)</em></label>
					<input type="text" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][icon]" value="<?php if ( $custom_icon ) echo $custom_icon; ?>" placeholder="<?php echo $original_icon; ?>" />
				</div><!-- .half -->
				<div class="half right" style="display:none;">
					<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][link]" value="<?php echo $original_link; ?>">
					<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][original_link]" value="<?php echo $original_link; ?>">
				</div>
			</div><!-- .np-extra-options -->
			<?php endif; ?>
			
			<?php 
			if ( isset($menu_item['submenu']) ) : 
			
			// Use the custom menu if available, otherwise use default
			if ( isset($nav_menu_options[$role['name']][$id]['submenu']) ){
				$submenu_items = $nav_menu_options[$role['name']][$id]['submenu'];
				$has_custom_submenu = true;
			} else {
				$submenu_items = $menu_item['submenu'];
				$has_custom_submenu = false;
			}
			?>
			<ul class="submenu-listing" data-np-sortable-admin-subnav>
				<?php 
				$si = 0;
				foreach ( $submenu_items as $submenu ) : 
				$label = ( $has_custom_submenu ) ? $submenu['label'] : $submenu[0];
				$role_name = ( $has_custom_submenu ) ?  $submenu['role'] : $submenu[1];
				$link = ( $has_custom_submenu ) ? $submenu['link'] : $submenu[2];
				$hidden_sub = ( $has_custom_submenu && isset($submenu['hidden']) && $submenu['hidden'] == 'true' ) ? true : false;
				if ( !array_key_exists($role_name, $role_capabilities) || !$role_capabilities[$role_name] ) continue; // This role doesn't have access to this item
				?>
				<li class="np-nav-preview <?php if ( $hidden_sub ) echo 'disabled'; ?> submenu-item" data-np-sortable-admin-nav>
					<div class="menu-item">
						<div class="handle">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class=" np-icon-menu"><path d="M0 0h24v24H0z" fill="none" /><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" class="bars" /></svg>
						</div>
						<div class="title">
							<p><?php echo $label; ?></p>
						</div>
						<div class="hide-checkbox">
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][submenu][<?php echo $si; ?>][label]" value="<?php echo $label; ?>" />
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][submenu][<?php echo $si; ?>][role]" value="<?php echo $role_name; ?>" />
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][submenu][<?php echo $si; ?>][link]" value="<?php echo $link; ?>" />
							<input type="checkbox" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][submenu][<?php echo $si; ?>][hidden]" value="true" data-nestedpages-admin-nav-item-checkbox <?php if ( $hidden_sub )  echo 'checked'; ?>/>
							<input type="hidden" name="nestedpages_admin[nav_menu_options][<?php echo $role['name']; ?>][<?php echo $id; ?>][submenu][<?php echo $si; ?>][order]" value="<?php echo $si; ?>" data-np-submenu-order>
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