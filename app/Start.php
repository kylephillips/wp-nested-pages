<?php 
/**
* Static Wrapper for Bootstrap Class
* Prevents T_STRING error when checking for 5.3.2
*/
class Start {

	public static function init()
	{
		global $np_env;
		$np_env = 'live';

		global $np_version;
		$np_version = '1.2.0';

		new NestedPages\Bootstrap;
	}
}