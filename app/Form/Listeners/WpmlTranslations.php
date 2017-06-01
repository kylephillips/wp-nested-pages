<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\PluginIntegration\WPML;

class WpmlTranslations extends BaseHandler 
{
	/**
	* Form Data
	*/
	protected $data;

	/**
	* Post Translations
	*/
	private $translations;

	/**
	* WPML Integration
	*/
	private $wpml;

	public function __construct()
	{
		parent::__construct();
		$this->wpml = new WPML;
		if ( !$this->wpml->installed ){
			$this->exception(__('WPML is not currently installed.', 'wp-nested-pages'));
			return;
		}
		$this->setPostId();
		$this->getTranslations();
		$this->organizeLanguages();
		if ( !is_array($this->translations) || sizeof($this->translations) == 0 ){
			$this->exception(__('There are currently no translations for the selected post.', 'wp-nested-pages'));
			return;
		}
		return wp_send_json(array('status' => 'success', 'translations' => $this->translations));
	}

	/**
	* Set the Post Id
	*/
	private function setPostId()
	{
		if ( !isset($_POST['post_id']) ){
			return $this->sendResponse(array('status' => 'error', 'message' => __('Post Not Found', 'nestedapges')));
		}
		$this->data['post_id'] = intval(sanitize_text_field($_POST['post_id']));
	}

	/**
	* Get all the translated versions of this post
	*/
	private function getTranslations()
	{
		$this->translations = $this->wpml->getAllTranslations($this->data['post_id']);
	}

	/**
	* Add Translation links
	*/
	private function organizeLanguages()
	{
		global $sitepress;
		$all_languages = $this->wpml->getLanguages();
		$current_language = $this->wpml->getCurrentLanguage();
		foreach ( $all_languages as $lang_code => $lang )
		{
			$add_link = 'post-new.php?' . http_build_query (
				array(
					'lang'        => $lang_code,
					'post_type'   => get_post_type ( $this->data['post_id'] ),
					'trid'        => $sitepress->get_element_trid($this->data['post_id']),
					'source_lang' => $current_language
				)
			);
			$all_languages[$lang_code]['add_link'] = $add_link;
			if ( array_key_exists($lang_code, $this->translations) ){
				$all_languages[$lang_code]['has_translation'] = true;
				$all_languages[$lang_code]['translation'] = $this->translations[$lang_code];
				$all_languages[$lang_code]['edit_link'] = get_edit_post_link($this->translations[$lang_code]->element_id);
			} else {
				$all_languages[$lang_code]['has_translation'] = false;
			}
		}
		$this->translations = $all_languages;
	}
}