<?php 
namespace NestedPages;

/**
* Helper Functions
*/
class Helpers 
{
	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		$url = plugins_url('/', NESTEDPAGES_URI);
		return rtrim($url, '/');
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(__FILE__) . '/Views/' . $file . '.php';
	}

	/**
	* Asset
	*/
	public static function asset($file)
	{
		return dirname(dirname(__FILE__)) . '/assets/' . $file;
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