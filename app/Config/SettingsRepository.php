<?php namespace NestedPages\Config;

class SettingsRepository {

	/**
	* Is the Datepicker UI option enabled
	* @return boolean
	*/
	public function datepickerEnabled()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['datepicker']) && $option['datepicker'] == 'true' ) return true;
		return false;
	}

}