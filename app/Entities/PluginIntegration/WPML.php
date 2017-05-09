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

	/**
	* Global Sitepress object
	*/
	private $sitepress;

	public function __construct()
	{
		if ( defined('ICL_SITEPRESS_VERSION') ){
			$this->installed = true;
			global $sitepress;
			$this->sitepress = $sitepress;
			return;
		} 
	}

	/**
	* Get the current language
	* @return string
	*/
	public function getCurrentLanguage($return_type = 'code')
	{
		if ( $return_type == 'code' ) return ICL_LANGUAGE_CODE;
		if ( $return_type == 'name' ) return ICL_LANGUAGE_NAME;
		if ( $return_type == 'name_en' ) return ICL_LANGUAGE_NAME_EN;
	}

	/**
	* Get all available languages
	* @return array
	*/
	public function getLanguages()
	{
		return icl_get_languages();
	}

	/**
	* Get the default language
	* @return array
	*/
	public function getDefaultLanguage()
	{
		return $this->sitepress->get_default_language();
	}

	/**
	* Get a single language
	* @return array
	*/
	public function getSingleLanguage($language = 'en')
	{
		$languages = $this->getLanguages();
		return ( array_key_exists($language, $languages) ) ? $languages[$language] : array();
	}

	/**
	* Is the default language currently being shown
	* @return boolean
	*/
	public function isDefaultLanguage()
	{
		return ( $this->getCurrentLanguage() == $this->getDefaultLanguage() ) ? true : false;
	}

	/**
	* Get all translations for a post
	* @return array
	*/
	public function getAllTranslations($post_id)
	{
		if ( !$post_id ) return array();
		$true_id = $this->sitepress->get_element_trid($post_id);
		return $this->sitepress->get_element_translations($true_id);
	}

}