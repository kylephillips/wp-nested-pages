<div class="np-menu-customization-header">
	<div class="role-select">
		<select data-np-nav-menu-user-role-select>
			<?php
				$s = 0;
				foreach ( $this->admin_menu_settings->roles as $select_role ){
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