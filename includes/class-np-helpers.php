<?php
/**
* Helper Functions
*/
class NP_Helpers {

	/**
	* Verify URL Format
	* @param string - URL to check
	* @return string - formatted URL
	*/
	public static function check_url($url)
	{
		$parsed = parse_url($url);
		if (empty($parsed['scheme'])) $url = 'http://' . ltrim($url, '/');
		return $url;
	}

	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		return plugins_url() . '/wp-simple-locator';
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(dirname(__FILE__)) . '/views/' . $file . '.php';
	}

}