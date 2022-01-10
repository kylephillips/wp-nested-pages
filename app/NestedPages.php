<?php 
/**
* Static Wrapper for Bootstrap Class
* Prevents T_STRING error when checking for 5.3.2
*/
class NestedPages 
{
	public static function init()
	{
		// dev/live
		global $np_env;
		$np_env = 'live';

		global $np_version;
		$np_version = '3.1.18';

		if ( is_admin() ) $app = new NestedPages\Bootstrap;
		if ( !is_admin() ) $app = new NestedPages\FrontEndBootstrap;
	}
}