<?php 
/**
* Static Wrapper for Bootstrap Class
* Prevents T_STRING error when checking for 5.3.2
*/
class Start {

	public static function init()
	{
		new NestedPages\Bootstrap;
	}
}