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
		return map_deep( $value , 'sanitize_text_field' );
	}
}