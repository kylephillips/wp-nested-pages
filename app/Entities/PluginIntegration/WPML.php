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

	/**
	* WPML Settings
	*/
	private $settings;

	public function __construct()
	{
		if ( defined('ICL_SITEPRESS_VERSION') ){
			$this->installed = true;
			global $sitepress;
			$this->sitepress = $sitepress;
			$this->settings = get_option('icl_sitepress_settings');
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
	* @return array or int
	*/
	public function getAllTranslations($post_id, $return = 'array')
	{
		if ( !$post_id && $return == 'array' ) return array();
		if ( !$post_id && $return == 'count' ) return 0;
		$true_id = $this->sitepress->get_element_trid($post_id);
		if ( $return == 'array' ) return $this->sitepress->get_element_translations($true_id);
		if ( $return == 'count' ) return count($this->sitepress->get_element_translations($true_id));
	}

	/**
	* Sync Post Order among translations
	* @param array of posts with children
	*/
	public function syncPostOrder($posts)
	{
		if ( $this->settings['sync_page_ordering'] !== 1 ) return;
		global $wpdb;
		if ( !is_array($posts) ) return;
		foreach ( $posts as $order => $post ) :
			$translations = $this->getAllTranslations($post['id']);
			foreach ( $translations as $lang_code => $post_info ) :
				$post_id = $post_info->element_id;
				$query = "UPDATE $wpdb->posts SET menu_order = '$order' WHERE ID = '$post_id'";
				$wpdb->query( $query );
			endforeach;
			if ( isset($post['children']) ) $this->syncPostOrder($post['children']);
		endforeach;
	}

	/**
	* Output the sync menus button
	* @return html
	*/
	public function syncMenusButton()
	{
		$url = esc_url(admin_url('admin.php?page=sitepress-multilingual-cms/menu/menu-sync/menus-sync.php'));
		return '<a href="' . $url . '" class="np-btn">' . __('Sync WPML Menus', 'wp-nested-pages') . '</a>';
	}

	/**
	* Get the Translated IDs of an array of post IDs
	* @param array $post_ids
	* @return array of post ids
	*/
	public function getAllTranslatedIds($post_ids)
	{
		if ( !is_array($post_ids) ) return array();
		foreach ( $post_ids as $id ){
			$translations = $this->getAllTranslations($id);
			if ( empty($translations) ) continue;
			foreach ( $translations as $lang => $post ){
				if ( $post->element_id == $id ) continue;
				array_push($post_ids, $post->element_id);
			}
		}
		return $post_ids;
	}

}