<?php 

namespace NestedPages\Entities\PluginIntegration;

use NestedPages\Entities\PluginIntegration\EditorialAccessManager;
use NestedPages\Entities\PluginIntegration\AdvancedCustomFields;
use NestedPages\Entities\PluginIntegration\YoastSeo;

class IntegrationFactory 
{

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
		$this->plugins->acf = new AdvancedCustomFields;
		$this->plugins->yoast = new YoastSeo;
	}

}