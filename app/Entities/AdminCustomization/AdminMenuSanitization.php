<?php
namespace NestedPages\Entities\AdminCustomization;

/**
* Sanitizes the custom admin menu options before saving
*/
class AdminMenuSanitization
{
	public function __construct()
	{
		add_filter('pre_update_option_nestedpages_admin', [$this, 'sanitizeOption'], 1, 3);
	}

	public function sanitizeOption($value, $old_value, $option)
	{
		if ( !isset($value['enabled_menu']) || $value['enabled_menu'] !== 'true') return $value;
		if ( !isset($value['nav_menu_options']) || empty($value['nav_menu_options'])) return $value;
		// We can't use map_deep, because it strips out core WP span tags necessary
		foreach ( $value['nav_menu_options'] as $role => $pages ) :
			foreach ( $pages as $page_id => $page_options ) :

				// Icon
				if ( isset($page_options['icon']) && $page_options['icon'] !== '' ) $value['nav_menu_options'][$role][$page_id]['icon'] = sanitize_text_field($page_options['icon']);

				// Label
				if ( isset($page_options['label']) && $page_options['label'] !== '' ) $value['nav_menu_options'][$role][$page_id]['label'] = sanitize_text_field($page_options['label']);
				
			endforeach;
		endforeach;
		return $value;
	}
}