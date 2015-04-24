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

	/**
	* Is the Menu Sync Option Visible
	*/
	public function hideMenuSync()
	{
		$option = get_option('nestedpages_ui', false);
		if ( $option && isset($option['hide_menu_sync']) && $option['hide_menu_sync'] == 'true' ) return true;
		return false;
	}

	/**
	* Is menu sync enabled?
	*/
	public function menuSyncEnabled()
	{
		$option = get_option('nestedpages_menusync');
		return ( $option == 'sync' ) ? true : false;
	}

}