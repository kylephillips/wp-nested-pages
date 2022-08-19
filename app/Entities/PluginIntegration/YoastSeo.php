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

	/**
	* Get the Score HTML
	* @return str|html - Yoast indicator
	*/
	public function getScore($post_id)
	{
		$yoast_score = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
		if ( ! $yoast_score ) {
			$yoast_score = get_post_meta($post_id, '_yoast_wpseo_linkdex', true);
			if ( version_compare(WPSEO_VERSION, '19.5.1', '<') ){
				$score = \WPSEO_Utils::translate_score($yoast_score);
				return '<span class="np-seo-indicator ' . esc_html($score) . '"></span>';
			}
			$score_icon_helper = YoastSEO()->helpers->score_icon;
			$score_icon_presenter = $score_icon_helper->for_readability($yoast_score);
			return $score_icon_presenter->present();
		} else {
			return '<span class="np-seo-indicator no-index"></span>';
		}
	}
}