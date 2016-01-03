<?php 

namespace NestedPages;

/**
* Helper Functions
*/
class Helpers 
{

	/**
	* Verify URL Format
	* @param string - URL to check
	* @return string - formatted URL
	*/
	public static function check_url($url)
	{
		return esc_url($url);
	}

	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		return plugins_url('/', NESTEDPAGES_URI);
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(__FILE__) . '/Views/' . $file . '.php';
	}

	/**
	* Link to the default WP Pages listing
	* @since 1.2
	* @return string
	*/
	public static function defaultPagesLink($type = 'page')
	{
		$link = esc_url( admin_url('edit.php?post_type=' . $type ) );
		return $link;
	}

}