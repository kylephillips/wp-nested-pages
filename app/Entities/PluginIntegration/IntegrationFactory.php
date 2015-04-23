<?php namespace NestedPages\Entities\PluginIntegration;

use NestedPages\Entities\PluginIntegration\EditorialAccessManager;

class IntegrationFactory {

	/**
	* Integration Classes
	*/
	public $plugins;

	public function __construct()
	{
		$this->build();
		return $this->plugins;
	}

	public function build()
	{
		$this->plugins = new \StdClass();
		$this->plugins->editorial_access_manager = new EditorialAccessManager;
	}

}