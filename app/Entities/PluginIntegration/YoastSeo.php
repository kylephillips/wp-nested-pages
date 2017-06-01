<?php 
namespace NestedPages\Entities\PluginIntegration;

/**
* Yoast SEO Integration
* @link https://wordpress.org/plugins/wordpress-seo/
*/
class YoastSeo 
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed = false;

	public function __construct()
	{
		if ( function_exists('wpseo_auto_load') ){
			$this->installed = true;
			return;
		} 
	}

	public function getScore($post_id)
	{
		$yoast_score = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
		if ( ! $yoast_score ) {
			$yoast_score = get_post_meta($post_id, '_yoast_wpseo_linkdex', true);
			return \WPSEO_Utils::translate_score($yoast_score);
		} else {
			return 'noindex';
		}
	}
}