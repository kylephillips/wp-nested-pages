<?php 

namespace NestedPages\Entities\PluginIntegration;

/**
* WPML Integration
* @link https://wpml.org/
*/

class WPML 
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed = false;

	public function __construct()
	{
		if ( defined('ICL_SITEPRESS_VERSION') ){
			$this->installed = true;
			return;
		} 
	}

}