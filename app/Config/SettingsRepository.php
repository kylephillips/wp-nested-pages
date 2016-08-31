<?php 

namespace NestedPages\Config;

class SettingsRepository 
{

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

	/**
	* Are menus completely disabled?
	* @return boolean
	*/
	public function menusDisabled()
	{
		$option = get_option('nestedpages_disable_menu');
		if ( $option && $option == 'true' ) return true;
		return false;
	}

	/**
	* Array of configurable standard fields
	* @return array
	*/
	public function standardFields()
	{
		$fields = array(
			'title' => 'Post Title', 
			'slug' => 'Slug', 
			'date' => 'Post Date', 
			'author' => 'Author', 
			'status' => 'Post Status', 
			'template' => 'Page Template', 
			'password' => 'Password/Private', 
			'allow_comments' => 'Allow Comments', 
			'hide_in_np' => 'Hide in Nested Pages',
			'menu_options' => 'Menu Options'
		);
		return $fields;
	}

}