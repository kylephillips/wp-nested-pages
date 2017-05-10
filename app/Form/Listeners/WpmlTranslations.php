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
		$this->addTranslationLinks();
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
	* Perform a search on posts
	*/
	private function getTranslations()
	{
		$this->translations = $this->wpml->getAllTranslations($this->data['post_id']);
	}

	/**
	* Add Translation links
	*/
	private function addTranslationLinks()
	{
		global $sitepress;
		foreach ( $this->translations as $code => $translation )
		{
			$add_link = 'post-new.php?' . http_build_query (
				array(
					'lang'        => $code,
					'post_type'   => get_post_type ( $this->data['post_id'] ),
					'trid'        => $sitepress->get_element_trid($translation->element_id),
					'source_lang' => $translation->source_language_code
				)
			);
			$this->translations[$code]->add_link = $add_link;
			$this->translations[$code]->edit_link = get_edit_post_link($translation->element_id);
		}
	}

}