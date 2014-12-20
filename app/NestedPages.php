<?php 
/**
* Static Wrapper for Bootstrap Class
* Prevents T_STRING error when checking for 5.3.2
*/
class NestedPages {

	public static function init()
	{
		// dev/live
		global $np_env;
		$np_env = 'dev';

		global $np_version;
		$np_version = '1.2.0';

		$app = new NestedPages\Bootstrap;
	}
}