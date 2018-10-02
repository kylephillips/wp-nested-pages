<?php
namespace NestedPages\Entities\PluginIntegration;

class DarkMode
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed = false;

	public function __construct()
	{
		if( !class_exists('Dark_Mode') ) return;
		$this->installed = true;
	}
}